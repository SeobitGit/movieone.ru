<?php

namespace App\Services\Back\Movies;

use App\Models\Comment;
use App\Models\MovieSpinoff;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

Class UpdateService
{
    public function update($data, $movie)
    {
        if(isset($data['poster']) && $data['poster'] !=null){
            File::delete(storage_path('/app/public/movies' . $movie->poster));
            $poster_path = '/posters'.'/poster'.$data['kinopoisk_id'].'.webp';
            $data['poster'] = Image::make($data['poster'])
                ->fit(267, 400)
                ->encode('webp', 90)
                ->save(storage_path('/app/public/movies/posters'.'/poster'.$data['kinopoisk_id'].'.webp'));
            $data['poster'] = $poster_path;
        }else{
            unset($data['poster']);
        }

        if(isset($data['backdrop']) && $data['backdrop'] !=null){
            File::delete(storage_path('/app/public/movies' . $movie->backdrop));
            File::delete(storage_path('/app/public/movies' . $movie->backdrop_min));

            $backdrop_path = '/backdrops'.'/backdrop'.$data['kinopoisk_id'].'.webp';
            Image::make($data['backdrop'])
                ->fit(1200, 450)
                ->encode('webp', 90)
                ->save(storage_path('/app/public/movies/backdrops'.'/backdrop'.$data['kinopoisk_id'].'.webp'));
            $backdrop_path_min = '/backdrops'.'/backdrop'.$data['kinopoisk_id'].'_min.webp';
            Image::make($data['backdrop'])
                ->fit(400, 150)
                ->encode('webp', 90)
                ->save(storage_path('/app/public/movies/backdrops'.'/backdrop'.$data['kinopoisk_id'].'_min.webp'));
            $data['backdrop'] = $backdrop_path;
            $data['backdrop_min'] = $backdrop_path_min;
        }else{
            unset($data['backdrop']);
        }

        $movie->genres()->sync($data['genres']);
        $movie->countries()->sync($data['countries']);

        $spinOffToDelete = MovieSpinoff::where('movie_id', $data['id'])->get();

        foreach ($spinOffToDelete as $item)
        {
            $item->delete();
        }
        if(isset($data['spin_off'])){
            foreach ($data['spin_off'] as $item) {
                MovieSpinoff::create([
                    'movie_id' => $movie->id,
                    'spin_off' => $item,
                ]);
            }
        }

        //Update Trailers
        $movie->trailers()->each(function ($trailer){
            $trailer->delete();
        });

        if(isset($data['trailers']) || !empty($data['trailers'])) {
            foreach ($data['trailers'] as $item) {
                $movie->trailers()->create([
                    'movie_id' => $movie->id,
                    'url' => $item['url'],
                    'name' => $item['name'],
                    'site' => $item['site'],
                ]);
            };
        }

        //Update Facts
        $movie->facts()->each(function ($fact){
            $fact->delete();
        });

        if(isset($data['facts']) || !empty($data['facts'])) {
            foreach ($data['facts'] as $item) {
                $movie->facts()->create([
                    'movie_id' => $movie->id,
                    'value' => $item['value'],
                    'type' => $item['type'],
                    'spoiler' => $item['spoiler'],
                ]);
            };
        }

        if(!isset($data['budget']) || $data['budget'] == '' || $data['budget'] == 'undefined undefined')
        {
            $data['budget'] = null;
        }

        unset($data['countries']);
        unset($data['genres']);
        unset($data['trailers']);
        unset($data['spin_off']);
        unset($data['facts']);

        $comments = Comment::where('approved', 1)
            ->where('movie_id', $data['id'])
            ->select('plot', 'actors_game', 'atmosphere')
            ->get()
            ->toArray();

        $rating = [];
        $rating['plot'] = $data['default_plot'];
        $rating['actors_game'] = $data['default_actors_game'];
        $rating['atmosphere'] = $data['default_atmosphere'];

        foreach ($comments as $item) {
            $rating['plot'] += $item['plot'];
            $rating['actors_game'] += $item['actors_game'];
            $rating['atmosphere'] += $item['atmosphere'];
        }


        $data['plot'] = round(($rating['plot'] / (count($comments) + 1)), 1);
        $data['actors_game'] = round(($rating['actors_game'] / (count($comments) + 1)), 1);
        $data['atmosphere'] = round(($rating['atmosphere'] / (count($comments) + 1)), 1);
        $data['rate'] = round(($data['plot'] + $data['actors_game'] + $data['atmosphere']) / 3, 1);


        //Update Sound Tracks

        if(isset($data['s_tracks_deleteIds']) && !empty($data['s_tracks_deleteIds']))
        {
            foreach ($data['s_tracks_deleteIds'] as $del_soundtrack) {
                $del = $movie->soundTracks()->where('id', $del_soundtrack)->first();
                File::delete(storage_path('/app/public/movies/soundtracks/' . $movie->id. '/'. $del->file));
                $del->delete();
            }

        }

        if (isset($data['new_s_tracks']) && !empty($data['new_s_tracks'])) {
            foreach ($data['new_s_tracks'] as $item)
            {

                Storage::disk('public')->putFileAs('/movies/soundtracks/'.$movie->id.'/', $item['file'] , $item['file']->getClientOriginalName());

                $movie->soundtracks()->create([
                    'movie_id' => $movie->id,
                    'file' => $item['file']->getClientOriginalName(),
                    'title' => $item['title'],
                ]);
            }
        }

        //Удаляем пустую папку
        $FileSystem = new Filesystem();
        $directory = storage_path('app/public/movies/soundtracks/'.$movie->id);

        if ($FileSystem->exists($directory)) {
            $files = $FileSystem->files($directory);
            if (empty($files)) {
                // Yes, delete the directory.
                $FileSystem->deleteDirectory($directory);
            }
        }


        unset($data['s_tracks_deleteIds']);
        unset($data['new_s_tracks']);


        $movie->update($data);
    }

}

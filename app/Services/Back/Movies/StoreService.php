<?php

namespace App\Services\Back\Movies;

use App\Models\Movie;
use App\Models\MovieSpinoff;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class StoreService
{
    public function store($data)
    {
        //Poster Upload
        Image::make($data['poster'])
            ->fit(267, 400)
            ->encode('webp', 90)
            ->save(storage_path('/app/public/movies/posters' . '/poster' . $data['kinopoiskId'] . '.webp'));
        //Backdrop Upload
        if (isset($data['backdrop'])) {
            Image::make($data['backdrop'])
                ->fit(1200, 450)
                ->encode('webp', 90)
                ->save(storage_path('/app/public/movies/backdrops' . '/backdrop' . $data['kinopoiskId'] . '.webp'));
            Image::make($data['backdrop'])
                ->fit(400, 150)
                ->encode('webp', 90)
                ->save(storage_path('/app/public/movies/backdrops' . '/backdrop' . $data['kinopoiskId'] . '_min.webp'));
        }

        $poster_path = '/posters' . '/poster' . $data['kinopoiskId'] . '.webp';

        if (isset($data['backdrop'])) {
            $backdrop_path = '/backdrops' . '/backdrop' . $data['kinopoiskId'] . '.webp';
            $backdrop_path_min = '/backdrops' . '/backdrop' . $data['kinopoiskId'] . '_min.webp';
        } else {
            $backdrop_path = null;
            $backdrop_path_min = null;
        }

        if (!isset($data['budget']) || $data['budget'] == '' || $data['budget'] == 'undefined undefined') {
            $data['budget'] = null;
        }

        $movie = Movie::firstOrCreate(
            ['kinopoisk_id' => $data['kinopoiskId']],
            [
                'kinopoisk_id' => $data['kinopoiskId'],
                'nameRu' => $data['nameRu'],
                'nameEn' => $data['nameEn'],
                'age_limits' => $data['age_limits'],
                'poster' => $poster_path,
                'backdrop' => $backdrop_path,
                'backdrop_min' => $backdrop_path_min,
                'type' => $data['type'],
                'category_id' => $data['category'],
                'year' => $data['year'],
                'duration' => $data['duration'],
                'plot' => $data['plot'],
                'default_plot' => $data['plot'],
                'actors_game' => $data['actors_game'],
                'default_actors_game' => $data['actors_game'],
                'atmosphere' => $data['atmosphere'],
                'default_atmosphere' => $data['atmosphere'],
                'rate' => $data['rate'],
                'budget' => $data['budget'],
                'slogan' => $data['slogan'],
                'description' => $data['description'],
                'title_id' => $data['title_id'],
                'meta_keywords' => $data['meta_keywords'],
                'meta_description' => $data['meta_description'],
            ]
        );

        $movie->genres()->attach($data['genres']);
        $movie->countries()->attach($data['countries']);
        if (isset($data['spin_off'])) {
            foreach ($data['spin_off'] as $item) {
                MovieSpinoff::create([
                    'movie_id' => $movie->id,
                    'spin_off' => $item,
                ]);
            }
        }

        if (isset($data['facts']) || !empty($data['facts'])) {
            foreach ($data['facts'] as $item) {
                $movie->facts()->create([
                    'movie_id' => $movie->id,
                    'value' => $item['value'],
                    'type' => $item['type'],
                    'spoiler' => $item['spoiler'],
                ]);
            }
        }


        if (isset($data['trailers']) || !empty($data['trailers'])) {
            foreach ($data['trailers'] as $item) {
                $movie->trailers()->create([
                    'movie_id' => $movie->id,
                    'url' => $item['url'],
                    'name' => $item['name'],
                    'site' => $item['site'],
                ]);
            }
        }


        //Handle SoundTracks

        if (isset($data['sound_tracks']) && !empty($data['sound_tracks'])) {
            foreach ($data['sound_tracks'] as $item)
            {
                Storage::disk('public')->putFileAs('/movies/soundtracks/'.$movie->id.'/', $item['file'] , $item['file']->getClientOriginalName());

                $movie->soundtracks()->create([
                   'movie_id' => $movie->id,
                   'file' => $item['file']->getClientOriginalName(),
                   'title' => $item['title'],
                ]);
            }
        }


    }


}

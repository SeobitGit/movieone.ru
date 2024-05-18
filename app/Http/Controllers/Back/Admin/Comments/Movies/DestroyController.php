<?php

namespace App\Http\Controllers\Back\Admin\Comments\Movies;

use App\Http\Controllers\BaseController;
use App\Models\Comment;

class DestroyController extends BaseController
{
    public function __invoke(Comment $comment)
    {
       $comment->delete();

        $comments = Comment::where('approved', 1)
            ->where('movie_id', $comment['movie_id'])
            ->select('plot', 'actors_game', 'atmosphere')
            ->get()
            ->toArray();

        $rating = [];
        $rating['plot'] = $comment->movie->default_plot;
        $rating['actors_game'] = $comment->movie->default_actors_game;
        $rating['atmosphere'] = $comment->movie->default_atmosphere;

        foreach ($comments as $item) {
            $rating['plot'] += $item['plot'];
            $rating['actors_game'] += $item['actors_game'];
            $rating['atmosphere'] += $item['atmosphere'];
        }

        $rating['plot'] = round(($rating['plot'] / (count($comments) + 1)), 1);
        $rating['actors_game'] = round(($rating['actors_game'] / (count($comments) + 1)), 1);
        $rating['atmosphere'] = round(($rating['atmosphere'] / (count($comments) + 1)), 1);

        $comment->movie()->update([
            'plot' => $rating['plot'],
            'actors_game' => $rating['actors_game'],
            'atmosphere' => $rating['atmosphere'],
            'rate' => round(($rating['plot'] + $rating['actors_game'] + $rating['atmosphere']) / 3 , 1),
        ]);

        $this->service->resetCache();

       return to_route('comments.index');
    }
}

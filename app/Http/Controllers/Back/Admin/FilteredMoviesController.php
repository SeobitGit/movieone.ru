<?php

namespace App\Http\Controllers\Back\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Admin\Collections\FilteredMoviesRequest;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;


class FilteredMoviesController extends Controller
{
    public function __invoke(FilteredMoviesRequest $request)
    {
        $data = $request->validated();

        if(isset($data['query_filter']))
        {
            $result = $data['query_filter'];
            $movies = Movie::where('nameRu', 'like', "%$result%")
                ->orWhere('nameEn', 'like', "%$result%")
                ->select('id', 'nameRu', 'nameEn', 'year')->orderBy('id', 'desc')->get();
        }else {
            $movies = DB::table('movies')->select('id', 'nameRu', 'nameEn', 'year')->orderBy('id', 'desc')->get();
        }

        return $movies;
    }
}

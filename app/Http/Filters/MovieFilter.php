<?php


namespace App\Http\Filters;


use App\Models\Category;
use App\Models\Country;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class MovieFilter extends AbstractFilter
{
    const CATEGORY = 'category';
    const GENRE = 'genre';
    const TYPE = 'type';
    const YEAR_FROM = 'yearFrom';
    const YEAR_TO = 'yearTo';
    const SEARCH = 'key';
    const COUNTRY= 'country';



    protected function getCallbacks(): array
    {
        return [
            self::CATEGORY => [$this, 'category'],
            self::GENRE => [$this, 'genre'],
            self::TYPE => [$this, 'type'],
            self::YEAR_FROM => [$this, 'yearFrom'],
            self::YEAR_TO => [$this, 'yearTo'],
            self::SEARCH => [$this, 'search'],
            self::COUNTRY => [$this, 'country'],
        ];
    }

    protected function category(Builder $builder, $value)
    {
        $cat = Cache::get("categories")->where('slug', $value)->first();

        $builder->where('category_id', $cat->id);
    }

    protected function type(Builder $builder, $value)
    {
        if($value === 'feature')
        {
            $type = 2;
        }else if($value === 'serial')
        {
            $type = 3;
        }else if($value === 'mini_serial')
        {
            $type = 4;
        }else
        {
        abort(404);
        }

        $builder->where('type', $type);
    }

    protected function search(Builder $builder, $value)
    {
        $builder->where('nameRu', 'like', "%$value%")
        ->orWhere('nameEn', 'like', "%$value%");
    }

    protected function yearFrom(Builder $builder, $value)
    {
        $builder->where('year', '>=', $value);
    }

    protected function yearTo(Builder $builder, $value)
    {
        $builder->where('year', '<=', $value);
    }

    protected function genre(Builder $builder, $value)
    {
        $builder->whereHas('genres', function ($b) use($value){
            $cat = Cache::get("categories")->where('slug', $this->getQueryParam('category'))->first();
            $genre = Cache::get("genres")
                ->where('category_id', $cat->id)
                ->where('slug', $value)->first();
            $b->where('genre_id', $genre->id);

        });
    }

    protected function country(Builder $builder, $value)
    {
        $builder->whereHas('countries', function ($b) use($value){
            $country = Cache::get("countries")->where('slug', $value)->first();
            $b->where('country_id', $country->id);

        });
    }

}

<?php

namespace App\Http\Requests\Back\Admin\Movies;

use Illuminate\Foundation\Http\FormRequest;
use phpDocumentor\Reflection\Types\Nullable;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'kinopoiskId'=>'required|string',
            'nameRu'=>'required|string',
            'nameEn'=>'required|string',
            'age_limits' => 'required|string',
            'countries' => 'required',
            'category'=>'required|string',
            'genres'=>'required',
            'year'=>'required|string',
            'duration'=>'required|string',
            'plot' => 'required|integer',
            'actors_game' => 'required|integer',
            'atmosphere' => 'required|integer',
            'rate'=>'required|string',
            'budget' =>'nullable|string',
            'slogan'=>'required|string|min:3|max:255',
            'description'=>'required|string|min:3|max:16300',
            'poster' => 'required|file',
            'backdrop' => 'nullable|file',
            'type'=>'required|string',
            'trailers' => 'nullable',
            'sequels' => 'nullable|array',
            'video_allowed' => 'required|boolean',
            'title_id' =>'required|string',
            'meta_keywords' => 'required|string|min:3|max:255',
            'meta_description' => 'required|string|min:3|max:16383',
            'spin_off' => 'nullable|array',
            'facts' => 'nullable|array',
            'sound_tracks' => 'nullable|array',
        ];
    }
}

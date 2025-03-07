<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:0',
            'poster_url' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'is_in_theaters' => 'nullable|boolean',
            'imdb_id' => 'nullable|string|max:20|unique:movies,imdb_id',
            'imdb_rating' => 'nullable|numeric|min:0|max:10',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Film başlığı zorunludur.',
            'title.max' => 'Film başlığı en fazla 255 karakter olabilir.',
            'duration.integer' => 'Film süresi bir tam sayı olmalıdır.',
            'duration.min' => 'Film süresi en az 0 olmalıdır.',
            'release_date.date' => 'Geçerli bir tarih formatı giriniz.',
            'imdb_id.unique' => 'Bu IMDb ID zaten kullanılıyor.',
            'imdb_rating.numeric' => 'IMDb puanı bir sayı olmalıdır.',
            'imdb_rating.min' => 'IMDb puanı en az 0 olmalıdır.',
            'imdb_rating.max' => 'IMDb puanı en fazla 10 olabilir.',
        ];
    }
} 
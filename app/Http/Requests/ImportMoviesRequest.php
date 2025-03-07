<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportMoviesRequest extends FormRequest
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
            'title' => 'required_without:count|string|max:255',
            'page' => 'nullable|integer|min:1',
            'count' => 'required_without:title|integer|min:1|max:30',
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
            'title.required_without' => 'Film başlığı veya sayı belirtmelisiniz.',
            'count.required_without' => 'Sayı veya film başlığı belirtmelisiniz.',
            'page.integer' => 'Sayfa numarası bir tam sayı olmalıdır.',
            'page.min' => 'Sayfa numarası en az 1 olmalıdır.',
            'count.integer' => 'Sayı bir tam sayı olmalıdır.',
            'count.min' => 'Sayı en az 1 olmalıdır.',
            'count.max' => 'Sayı en fazla 30 olabilir.',
        ];
    }
} 
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCinemaHallRequest extends FormRequest
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
            'cinema_id' => 'required|exists:cinemas,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:0',
            'type' => 'nullable|string|max:50',
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
            'cinema_id.required' => 'Sinema seçimi zorunludur.',
            'cinema_id.exists' => 'Seçilen sinema geçerli değil.',
            'name.required' => 'Salon adı zorunludur.',
            'capacity.required' => 'Kapasite zorunludur.',
            'capacity.integer' => 'Kapasite bir sayı olmalıdır.',
            'capacity.min' => 'Kapasite en az 0 olmalıdır.',
        ];
    }
} 
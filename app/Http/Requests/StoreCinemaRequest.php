<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCinemaRequest extends FormRequest
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
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'total_capacity' => 'required|integer|min:0',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
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
            'city_id.required' => 'Şehir seçimi zorunludur.',
            'city_id.exists' => 'Seçilen şehir geçerli değil.',
            'name.required' => 'Sinema adı zorunludur.',
            'address.required' => 'Adres zorunludur.',
            'total_capacity.required' => 'Toplam kapasite zorunludur.',
            'total_capacity.min' => 'Toplam kapasite en az 0 olmalıdır.',
        ];
    }
} 
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeatRequest extends FormRequest
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
            'cinema_hall_id' => 'sometimes|exists:cinema_halls,id',
            'seat_data' => 'sometimes|json',
            'status' => 'sometimes|string|in:active,inactive,maintenance',
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
            'cinema_hall_id.exists' => 'Seçilen salon geçerli değil.',
            'seat_data.json' => 'Koltuk verisi geçerli bir JSON formatında olmalıdır.',
            'status.in' => 'Durum değeri geçerli değil. Geçerli değerler: active, inactive, maintenance',
        ];
    }
} 
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShowtimeRequest extends FormRequest
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
            'movie_id' => 'required|exists:movies,id',
            'cinema_hall_id' => 'required|exists:cinema_halls,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'price' => 'required|numeric|min:0',
            'available_seats' => 'required|integer|min:0',
            'seat_status' => 'nullable|json',
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
            'movie_id.required' => 'Film seçimi zorunludur.',
            'movie_id.exists' => 'Seçilen film geçerli değil.',
            'cinema_hall_id.required' => 'Salon seçimi zorunludur.',
            'cinema_hall_id.exists' => 'Seçilen salon geçerli değil.',
            'start_time.required' => 'Başlangıç zamanı zorunludur.',
            'start_time.date' => 'Başlangıç zamanı geçerli bir tarih olmalıdır.',
            'end_time.required' => 'Bitiş zamanı zorunludur.',
            'end_time.date' => 'Bitiş zamanı geçerli bir tarih olmalıdır.',
            'end_time.after' => 'Bitiş zamanı başlangıç zamanından sonra olmalıdır.',
            'price.required' => 'Fiyat zorunludur.',
            'price.numeric' => 'Fiyat bir sayı olmalıdır.',
            'price.min' => 'Fiyat en az 0 olmalıdır.',
            'available_seats.required' => 'Müsait koltuk sayısı zorunludur.',
            'available_seats.integer' => 'Müsait koltuk sayısı bir tam sayı olmalıdır.',
            'available_seats.min' => 'Müsait koltuk sayısı en az 0 olmalıdır.',
            'seat_status.json' => 'Koltuk durumu geçerli bir JSON formatında olmalıdır.',
        ];
    }
} 
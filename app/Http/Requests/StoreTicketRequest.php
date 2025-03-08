<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_number' => 'required|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:reserved,confirmed,cancelled',
            'ticket_code' => 'nullable|string|unique:tickets,ticket_code',
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
            'user_id.required' => 'Kullanıcı seçimi zorunludur.',
            'user_id.exists' => 'Seçilen kullanıcı geçerli değil.',
            'showtime_id.required' => 'Seans seçimi zorunludur.',
            'showtime_id.exists' => 'Seçilen seans geçerli değil.',
            'seat_number.required' => 'Koltuk numarası zorunludur.',
            'seat_number.string' => 'Koltuk numarası metin formatında olmalıdır.',
            'seat_number.max' => 'Koltuk numarası en fazla 10 karakter olabilir.',
            'price.numeric' => 'Fiyat bir sayı olmalıdır.',
            'price.min' => 'Fiyat en az 0 olmalıdır.',
            'status.string' => 'Durum metin formatında olmalıdır.',
            'status.in' => 'Durum değeri geçerli değil. Geçerli değerler: reserved, confirmed, cancelled.',
            'ticket_code.string' => 'Bilet kodu metin formatında olmalıdır.',
            'ticket_code.unique' => 'Bu bilet kodu zaten kullanılmaktadır.',
        ];
    }
} 
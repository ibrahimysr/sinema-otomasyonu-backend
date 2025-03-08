<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
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
            'user_id' => 'sometimes|exists:users,id',
            'showtime_id' => 'sometimes|exists:showtimes,id',
            'seat_number' => 'sometimes|string|max:10',
            'price' => 'nullable|numeric|min:0',
            'status' => 'sometimes|string|in:reserved,confirmed,cancelled',
            'ticket_code' => 'nullable|string|unique:tickets,ticket_code,' . $this->route('id'),
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
            'user_id.exists' => 'Seçilen kullanıcı geçerli değil.',
            'showtime_id.exists' => 'Seçilen seans geçerli değil.',
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
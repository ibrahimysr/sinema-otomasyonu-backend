<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'ticket_id' => 'sometimes|exists:tickets,id',
            'amount' => 'nullable|numeric|min:0',
            'payment_method' => 'sometimes|string|in:credit_card,cash,bank_transfer,online',
            'status' => 'sometimes|string|in:pending,completed,failed',
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
            'ticket_id.exists' => 'Seçilen bilet geçerli değil.',
            'amount.numeric' => 'Tutar bir sayı olmalıdır.',
            'amount.min' => 'Tutar en az 0 olmalıdır.',
            'payment_method.string' => 'Ödeme yöntemi metin formatında olmalıdır.',
            'payment_method.in' => 'Ödeme yöntemi geçerli değil. Geçerli değerler: credit_card, cash, bank_transfer, online.',
            'status.string' => 'Durum metin formatında olmalıdır.',
            'status.in' => 'Durum değeri geçerli değil. Geçerli değerler: pending, completed, failed.',
        ];
    }
} 
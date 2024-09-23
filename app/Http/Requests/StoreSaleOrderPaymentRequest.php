<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleOrderPaymentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'sale_order_id' => 'required|integer',
            'dealer_id' => 'required|numeric',
            'amount' => 'required|numeric',
            'reference' => 'nullable|string',
            'paid_at' => 'required|date', //'required|date|date_format:Y-m-d'
        ];
    }
}

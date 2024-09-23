<?php

namespace App\Http\Requests;

use App\Models\PurchaseOrderInvoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePurchaseOrderInvoiceRequest extends FormRequest
{
    protected function failedAuthorization()
    {
        throw new AuthorizationException(trans('app.unauthorized'));
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit purchase orders');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => PurchaseOrderInvoice::DRAFT,
            'user_id' => Auth::user()->id,
            'invoice_number_slug' => str_replace([' ', '/'], '-', $this->invoice_number),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'invoice_number' => 'required|unique:purchase_order_invoices|max:255',
            'status' => 'required|integer',
            'user_id' => 'required|integer',
            'shipped_at' => 'required|date',
            'invoice_number_slug' => 'required',
            'payment_terms' => 'nullable|string',
        ];
    }
}

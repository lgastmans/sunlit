<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\PurchaseOrderInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderInvoiceRequest extends FormRequest
{
    protected function failedAuthorization()
    {
        throw new AuthorizationException(trans('app.unauthorized'));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit purchase orders');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => PurchaseOrderInvoice::DRAFT,
            'user_id' => Auth::user()->id,
            'invoice_number_slug' => str_replace(array(' ', '/'), '-', $this->invoice_number)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
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

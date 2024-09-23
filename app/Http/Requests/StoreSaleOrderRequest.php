<?php

namespace App\Http\Requests;

use App\Models\SaleOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreSaleOrderRequest extends FormRequest
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
    public function authorize(): bool
    {
        return $this->user()->can('edit sale orders');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => SaleOrder::DRAFT,
            'user_id' => Auth::user()->id,
            'order_number_slug' => str_replace([' ', '/'], '-', $this->order_number), //Str::of($this->order_number)->slug('-')
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order_number' => 'required|unique:sale_orders|max:255',
            'order_number_slug' => 'required',
            'dealer_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'status' => 'required|integer',
            'user_id' => 'required|integer',
            'payment_terms' => 'nullable|string',
            'shipping_state_id' => 'integer|nullable',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_address3' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_zip_code' => 'nullable|string|max:255',
            'shipping_gstin' => 'nullable|string|max:255',
            'shipping_contact_person' => 'nullable|string|max:255',
            'shipping_contact_person2' => 'nullable|string|max:255',
            'shipping_phone' => 'nullable|string|max:255',
            'shipping_phone2' => 'nullable|string|max:255',
            'shipping_email' => 'nullable|string|max:255',
            'shipping_email2' => 'nullable|string|max:255',
        ];
    }
}

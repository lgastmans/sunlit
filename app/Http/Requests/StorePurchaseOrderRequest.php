<?php

namespace App\Http\Requests;

use App\Models\PurchaseOrder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePurchaseOrderRequest extends FormRequest
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
            'status' => PurchaseOrder::DRAFT,
            'user_id' => Auth::user()->id,
            'order_number_slug' => str_replace([' ', '/'], '-', $this->order_number), //Str::of($this->order_number)->slug('-')
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'warehouse_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'order_number' => 'required|unique:purchase_orders|max:255',
            'order_number_slug' => 'required',
            'status' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }
}

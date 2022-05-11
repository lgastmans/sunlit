<?php

namespace App\Http\Requests;

use App\Models\SaleOrder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

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
    public function authorize()
    {
        return $this->user()->can('edit sale orders');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => SaleOrder::DRAFT,
            'user_id' => Auth::user()->id,
            'order_number_slug' => str_replace(array(' ', '/'), '-', $this->order_number) //Str::of($this->order_number)->slug('-')
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
            'order_number' => 'required|unique:sale_orders|max:255',
            'order_number_slug' => 'required',
            'dealer_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'status' => 'required|integer',
            'user_id' => 'required|integer'
        ];
    }
}

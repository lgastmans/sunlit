<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;


class StoreProductRequest extends FormRequest
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
        return $this->user()->can('edit products');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'code' => NULL,
            'name' => NULL,
            //'model' => NULL,
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
            'category_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'tax_id' => 'required|integer',
            'code' => 'max:255',
            'name' => 'max:255',
            'model' => 'max:255',
            'purchase_price' => 'max:255',
            // 'minimum_quantity' => 'integer',
            'kw_rating' => 'max:255',
            'part_number' => 'max:255|unique:products,part_number,NULL,id,deleted_at,NULL'.$this->id,
            'notes' => 'nullable',
            // 'cable_length_input' => 'numeric',
            // 'cable_length_output' => 'numeric',
            // 'weight_actual' => 'numeric',
            // 'weight_volume' => 'numeric',
            // 'weight_calculated' => 'numeric',
            // 'warranty' => 'integer'
        ];
    }
}

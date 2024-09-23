<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

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
    public function authorize(): bool
    {
        return $this->user()->can('edit products');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'code' => null,
            'name' => null,
            //'model' => NULL,
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
            'category_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'tax_id' => 'required|integer',
            'code' => 'max:255',
            'name' => 'max:255',
            'model' => 'max:255',
            'purchase_price' => 'max:255',
            'minimum_quantity' => 'integer|nullable',
            'kw_rating' => 'max:255',
            'part_number' => 'max:255|unique:products,part_number,NULL,id,deleted_at,NULL'.$this->id,
            'notes' => 'nullable',
            'cable_length_input' => 'numeric|nullable',
            'cable_length_output' => 'numeric|nullable',
            'weight_actual' => 'numeric|nullable',
            'weight_volume' => 'numeric|nullable',
            'weight_calculated' => 'numeric|nullable',
            'warranty' => 'integer|nullable',
        ];
    }
}

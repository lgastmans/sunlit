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
        //$b = str_replace(',', '', $this->display_purchase_price );
        //$b = filter_var( str_replace(",", "", $this->display_purchase_price), FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_FRACTION);
        //$b = preg_replace('/&.*?;/', '', $this->display_purchase_price);
        $b = preg_replace( '/[^.\d]/', '', $this->display_purchase_price);


//        dd($b);

        if (!is_numeric($b) || (empty($b)))
            $b = 0;

        $this->merge([
            'purchase_price' => $b * 100,
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
            'code' => 'required|max:255|unique:products,code,NULL,id,deleted_at,NULL'.$this->id,
            'name' => 'required_without:name|string|max:255',
            'model' => 'max:255',
            'display_purchase_price'=>'required',
            'purchase_price' => 'max:255',
            'minimum_quantity' => 'integer',
            'cable_length' => 'max:255',
            'kw_rating' => 'max:255',
            'part_number' => 'max:255',
            'notes' => 'nullable'
        ];
    }
}

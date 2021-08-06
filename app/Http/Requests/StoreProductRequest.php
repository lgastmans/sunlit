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
            'cable_length' => 'max:255',
            'kw_rating' => 'max:255',
            'part_number' => 'max:255',
            'notes' => 'nullable'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
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
        return $this->user()->can('edit warehouses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer',
            'address' => 'required',
            'address2' => 'max:255',
            'city' => 'required|required',
            'zip_code' => 'required|numeric',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'phone2' => 'required|string',
            'email' => 'required|email|unique:warehouses,email,'.$this->id,
        ];
    }
}

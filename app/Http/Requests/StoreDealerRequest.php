<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;


class StoreDealerRequest extends FormRequest
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
        return $this->user()->can('edit dealers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company' => 'required|string|max:255',
            'state_id' => 'required|integer',
            'address' => 'required',
            'address2' => 'max:255',
            'city' => 'required|required',
            'zip_code' => 'required|numeric',
            'gstin' => 'required|string',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'phone2' => 'required|string',
            'email' => 'required|email|unique:dealers,email,'.$this->id,

            'has_shipping_address' => 'boolean',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_state_id' => 'nullable|integer',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string',
            'shipping_zip_code' => 'nullable|numeric',
            'shipping_gstin' => 'nullable|string',
            'shipping_contact_person' => 'nullable|string',
            'shipping_phone' => 'nullable|string',
            'shipping_phone2' => 'nullable|string',
            'shipping_email' => 'nullable|email'
        ];
    }
}

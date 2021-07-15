<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'email' => 'required|email|unique:dealers,email,'.$this->id
        ];
    }
}

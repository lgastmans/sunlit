<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use \App\Http\Requests\StoreStateRequest;

use Illuminate\Auth\Access\AuthorizationException;

class StoreStateRequest extends FormRequest
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
        //return $this->user()->can('edit zones');
        return true;
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
            'code' => 'required|numeric',
            'abbreviation' => 'max:255',
            'freight_zone_id' => 'numeric'
//            'email' => 'required|email|unique:suppliers,email,'.$this->id
        ];
    }
}

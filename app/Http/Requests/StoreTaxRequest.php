<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;



class StoreTaxRequest extends FormRequest
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
        return $this->user()->can('edit taxes');
    }

    protected function prepareForValidation()
{
    $this->merge([
        'amount' => $this->display_amount * 100,
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
            'name' => 'required|max:255|unique:taxes,name,'.$this->id,
            'display_amount' => 'required',
            'amount' => 'max:255'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        return $this->user()->can('edit categories');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:categories,name,NULL,id,deleted_at,NULL'.$this->id,
            'hsn_code' => 'max:255',
            'customs_duty' => 'required',
            'social_welfare_surcharge' => 'required',
            'igst' => 'required',
        ];
    }
}

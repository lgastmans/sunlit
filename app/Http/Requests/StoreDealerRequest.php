<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    public function authorize(): bool
    {
        return $this->user()->can('edit dealers');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
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
            'phone2' => 'nullable|max:255|string',
            'email2' => 'nullable|max:255|string',
            'email3' => 'nullable|max:255|string',
            'email' => 'nullable|max:255|string',
            //'email' => 'required|email|unique:dealers,email,'.$this->id,
            //'email' => ['required', Rule::unique('dealers')->whereNull('deleted_at')],

            'has_shipping_address' => 'boolean',
            'shipping_company' => 'nullable|string|max:255',
            'shipping_state_id' => 'nullable|integer',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_address2' => 'nullable|string|max:255',
            'shipping_address3' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string',
            'shipping_zip_code' => 'nullable|numeric',
            'shipping_gstin' => 'nullable|string',
            'shipping_contact_person' => 'nullable|string',
            'shipping_contact_person2' => 'nullable|string',
            'shipping_phone' => 'nullable|string',
            'shipping_phone2' => 'nullable|string',
            'shipping_email' => 'nullable|email',
            'shipping_email2' => 'nullable|email',
        ];
    }

    /**
     * Prepare inputs for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has_shipping_address == null) {
            $has_shipping_address = 0;
        } else {
            $has_shipping_address = 1;
        }
        $this->merge([
            'has_shipping_address' => $has_shipping_address,
        ]);
    }

    /**
     * Convert to boolean
     *
     * @return bool
     */
    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}

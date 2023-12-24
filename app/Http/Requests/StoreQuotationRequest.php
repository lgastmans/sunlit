<?php

namespace App\Http\Requests;

use App\Models\Quotation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    
    protected function prepareForValidation()
    {
        $this->merge([
            'status' => Quotation::DRAFT,
            'user_id' => Auth::user()->id,
            'quotation_number_slug' => str_replace(array(' ', '/'), '-', $this->quotation_number)
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
            'quotation_number' => 'required|unique:quotations|max:255',
            'quotation_number_slug' => 'required',
            'dealer_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'status' => 'required|integer',
            'user_id' => 'required|integer',
        ];
    }
}

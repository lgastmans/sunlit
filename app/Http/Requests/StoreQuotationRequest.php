<?php

namespace App\Http\Requests;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreQuotationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'status' => Quotation::DRAFT,
            'user_id' => Auth::user()->id,
            'quotation_number_slug' => str_replace([' ', '/'], '-', $this->quotation_number),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
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

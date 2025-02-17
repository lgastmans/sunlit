<?php

namespace App\Http\Requests;

use App\Models\CreditNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCreditNoteRequest extends FormRequest
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
            'status' => CreditNote::DRAFT,
            'user_id' => Auth::user()->id,
            'credit_note_number_slug' => str_replace([' ', '/'], '-', $this->credit_note_number),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'credit_note_number' => 'required|unique:credit_notes|max:255',
            'credit_note_number_slug' => 'required',
            'dealer_id' => 'required|integer',
            'warehouse_id' => 'required|integer',
            'status' => 'required|integer',
            'user_id' => 'required|integer',
            'invoice_number' => 'nullable|max:255',
            'invoice_date' => 'nullable|date',
            'is_against_invoice' => 'integer',
        ];
    }
}

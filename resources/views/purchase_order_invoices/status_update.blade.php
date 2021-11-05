




{{-- customs --}}
<div class="mt-lg-0 rounded @if ($invoice->status != 4) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="customs-order-form" class="needs-validation" novalidate
                action="{{ route('purchase-order-invoices.customs', $invoice->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-4" id="customs_at">
                        <label class="form-label">Due date</label>
                        <input type="text" class="form-control" name="customs_at" value="{{ $invoice->display_customs_at }}"
                        data-provide="datepicker" 
                        data-date-container="#customs_at"
                        data-date-autoclose="true"
                        data-date-format="M d, yyyy"
                        required>
                        <div class="invalid-feedback">
                            Customs date is required
                        </div>
                    </div>
                    <div class="col-xl-8" id="boe_number">
                        <label class="form-label">Bill of Entry #</label>
                        <input type="text" class="form-control" name="boe_number" required>
                    </div>
                </div>
                
             
                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="custom_order">Order at Customs</button>
            </form>

        </div>
    </div>
</div>

{{-- cleared --}}
<div class="mt-lg-0 rounded @if ($invoice->status != 5) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="clear-order-form" class="needs-validation" novalidate
                action="{{ route('purchase-order-invoices.cleared', $invoice->id) }}" method="POST">
                @csrf()
                @method('PUT')
               <div class="row mb-3">
                    <div class="col-xl-4" id="cleared_at">
                        <label class="form-label">Cleared date</label>
                        <input type="text" class="form-control" name="cleared_at" value="{{ $invoice->display_cleared_at }}"
                        data-provide="datepicker" 
                        data-date-container="#cleared_at"
                        data-date-autoclose="true"
                        data-date-format="M d, yyyy"
                        required>
                        <div class="invalid-feedback">
                            Cleared date is required
                        </div>
                    </div>
                     <div class="col-xl-6">
                        <label class="form-label">Customs Exchange Rate</label>
                        <div class="input-group">
                            <span class="input-group-text" id="cleared__currency">{{ __('app.currency_symbol_inr')}}</span>
                            <input class="form-control" id="customs_exchange_rate" name="customs_exchange_rate" value="{{ old('customs_exchange_rate', $invoice->customs_exchange_rate) }}" required >
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'customs exchange rate']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-xl-10">
                        <label class="form-label">Amount at Customs</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                            <input class="form-control" id="customs_amount" name="customs_amount" required />
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'customs amount']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="cleared_order">Clear order</button>
            </form>

        </div>
    </div>
</div>

{{-- received --}}
<div class="mt-lg-0 rounded @if ($invoice->status != 6) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="receive-order-form"  class="needs-validation" novalidate
                action="{{ route('purchase-order-invoices.received', $invoice->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-6" id="received_at">
                        <label class="form-label">Received date</label>
                        <input type="text" class="form-control" name="received_at"
                            value="{{ $invoice->display_received_at }}" data-provide="datepicker" data-date-autoclose="true"
                            data-date-container="#received_at" data-date-format="M d, yyyy" required>
                    </div>
                </div>
            
                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="ship_order">Receive order</button>
            </form>

        </div>
    </div>
</div>

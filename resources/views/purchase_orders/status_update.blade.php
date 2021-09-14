{{-- ordered --}}
{{-- <div class="mt-lg-0 rounded @if ($purchase_order->status > 2) d-none @else d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="place-order-form"  class="needs-validation" novalidate
                action="{{ route('purchase-orders.update', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <input type="hidden" name="field" value="ordered_at">
                <div class="mb-3 position-relative" id="ordered_at">
                    <label class="form-label">Ordered date</label>
                    <input type="text" class="form-control" name="ordered_at"
                        value="{{ $purchase_order->display_ordered_at }}" data-provide="datepicker" data-date-autoclose="true"
                        data-date-container="#ordered_at" data-date-format="M d, yyyy">
                        <div class="invalid-feedback">
                            Ordered date is required
                        </div>    
                </div>
                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="place_order">Update order</button>
            </form>

        </div>
    </div>
</div> --}}

{{-- confirmed --}}
<div class="mt-lg-0 rounded @if ($purchase_order->status != 2) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="confirm-order-form"  class="needs-validation" novalidate
                action="{{ route('purchase-orders.confirmed', $purchase_order->id) }}" method="POST" >
                @csrf()
                @method('PUT')
                <div class="mb-3 position-relative" id="confirmed_at">
                    <label class="form-label">Confirmation date</label>
                    <input type="text" class="form-control" name="confirmed_at"
                        value="{{ $purchase_order->display_confirmed_at }}" data-provide="datepicker" data-date-autoclose="true"
                        data-date-container="#confirmed_at" data-date-format="M d, yyyy" required>
                        <div class="invalid-feedback">
                            Confirmation date is required
                        </div>
                </div>
                <button class="col-lg-12 text-center btn btn-danger" type="submit"
                    name="confirm_order">Confirm order</button>
            </form>

        </div>
    </div>
</div>

{{-- shipped --}}
<div class="mt-lg-0 rounded @if ($purchase_order->status != 3) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="ship-order-form"  class="needs-validation" novalidate
                action="{{ route('purchase-orders.shipped', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-4" id="shipped_at">
                        <label class="form-label">Shipped date</label>
                        <input type="text" class="form-control" name="shipped_at"
                            value="{{ $purchase_order->display_shipped_at }}" data-provide="datepicker" data-date-autoclose="true"
                            data-date-container="#shipped_at" data-date-format="M d, yyyy" required>
                    </div>
                    <div class="col-xl-4 offset-xl-2" id="due_at">
                        <label class="form-label">Due date</label>
                        <input type="text" class="form-control" name="due_at"
                            value="{{ $purchase_order->display_due_at }}" data-provide="datepicker" data-date-autoclose="true"
                            data-date-container="#due_at" data-date-format="M d, yyyy" required>
                    </div>
                </div>
                
                <div class="row mb-3" >
                    <div class="col-xl-6" id="courier">
                        <label class="form-label">Courier</label>
                        <input type="text" class="form-control" name="courier" required>
                    </div>
                    <div class="col-xl-6" id="tracking_number">
                        <label class="form-label">Tracking number</label>
                        <input type="text" class="form-control" name="tracking_number" required>
                    </div>
                </div>
                <button class="col-lg-12 text-center btn btn-danger" type="submit"
                    name="ship_order">Ship order</button>
            </form>

        </div>
    </div>
</div>

{{-- customs --}}
<div class="mt-lg-0 rounded @if ($purchase_order->status != 4) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="customs-order-form" class="needs-validation" novalidate
                action="{{ route('purchase-orders.customs', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-4" id="customs_at">
                        <label class="form-label">Customs date</label>
                        <input type="text" class="form-control" name="customs_at"
                            value="{{ $purchase_order->display_customs_at }}" data-provide="datepicker" data-date-autoclose="true"
                            data-date-container="#customs_at" data-date-format="M d, yyyy" required>
                    </div>
                    <div class="col-xl-8" id="boe_number">
                        <label class="form-label">Bill of Entry #</label>
                        <input type="text" class="form-control" name="boe_number" required>
                    </div>
                </div>
                
             
                <button class="col-lg-12 text-center btn btn-danger" type="submit"
                    name="custom_order">Order at Customs</button>
            </form>

        </div>
    </div>
</div>

{{-- cleared --}}
<div class="mt-lg-0 rounded @if ($purchase_order->status != 5) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="clear-order-form" class="needs-validation" novalidate
                action="{{ route('purchase-orders.cleared', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-4" id="cleared_at">
                        <label class="form-label">Cleared date</label>
                        <input type="text" class="form-control" name="cleared_at"
                            value="{{ $purchase_order->display_cleared_at }}" data-provide="datepicker" data-date-autoclose="true"
                            data-date-container="#cleared_at" data-date-format="M d, yyyy" required>
                    </div>
                    <div class="col-xl-5">
                        <label class="form-label">Customs Exchange Rate</label>
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroupPrepend">{{ __('app.currency_symbol_inr')}}</span>
                            <input class="form-control" id="customs_exchange_rate" name="customs_exchange_rate" value="{{ old('customs_exchange_rate', $purchase_order->customs_exchange_rate) }}" data-toggle="input-mask"/>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'order_exchange_rate']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-xl-9">
                        <label class="form-label">Amount at Customs</label>
                        <div class="input-group">
                            <span class="input-group-text" id="inputGroupPrepend">{{ __('app.currency_symbol_inr')}}</span>
                            <input class="form-control" id="customs_amount" name="customs_amount" data-toggle="input-mask"/>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'order_exchange_rate']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <button class="col-lg-12 text-center btn btn-danger" type="submit"
                    name="cleared_order">Clear order</button>
            </form>

        </div>
    </div>
</div>

{{-- received --}}
<div class="mt-lg-0 rounded @if ($purchase_order->status != 6) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="receive-order-form"  class="needs-validation" novalidate
                action="{{ route('purchase-orders.received', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-6" id="received_at">
                        <label class="form-label">Received date</label>
                        <input type="text" class="form-control" name="received_at"
                            value="{{ $purchase_order->display_received_at }}" data-provide="datepicker" data-date-autoclose="true"
                            data-date-container="#received_at" data-date-format="M d, yyyy" required>
                    </div>
                </div>
            
                <button class="col-lg-12 text-center btn btn-danger" type="submit"
                    name="ship_order">Receive order</button>
            </form>

        </div>
    </div>
</div>
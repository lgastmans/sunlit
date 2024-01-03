
{{-- booked --}}
<div class="mt-lg-0 rounded @if ($order->status != 1) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="confirm-order-form" class="needs-validation" novalidate
                action="{{ route('sale-orders.booked', $order->id) }}" method="POST" >
                @csrf()
                @method('PUT')
                <div class="mb-3 position-relative" id="booked_at">
                    <label class="form-label">Booked date</label>
                    <input type="text" class="form-control" name="booked_at" value="{{ $order->display_booked_at }}"
                    data-provide="datepicker" 
                    data-date-container="#booked_at"
                    data-date-autoclose="true"
                    data-date-format="M d, yyyy"
                    data-date-start-date="-1m"
                    data-date-end-date="+6m"
                    data-date-today-highlight="true"
                    required>
                    <div class="invalid-feedback">
                        Booked date is required
                    </div>
                </div>
                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="confirm_order">Book order</button>
            </form>

        </div>
    </div>
</div>

{{-- shipped --}}
<div class="mt-lg-0 rounded @if ($order->status != 3) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="ship-order-form" class="needs-validation" novalidate
                action="{{ route('sale-orders.dispatched', $order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-4" id="dispatched_at">
                        <label class="form-label">Dispatched date</label>
                        <input type="text" class="form-control" name="dispatched_at" value="{{ $order->display_dispatched_at }}"
                        data-provide="datepicker" 
                        data-date-container="#dispatched_at"
                        data-date-autoclose="true"
                        data-date-format="M d, yyyy"
                        data-date-start-date="-1m"
                        data-date-end-date="+6m"
                        data-date-today-highlight="true"
                        required>
                        <div class="invalid-feedback">
                            Dispatched date is required
                        </div>
                    </div>
                    <div class="col-xl-6 " id="courier">
                        <label class="form-label">Courier</label>
                        <input type="text" class="form-control" name="courier" required>
                        <div class="invalid-feedback">
                            Courier is required
                        </div>
                    </div>
                    
                </div>
                
                <div class="row mb-3" >
                    <div class="col-xl-4" id="due_at">
                        <label class="form-label">Due date</label>
                        <input type="text" class="form-control" name="due_at" value="{{ $order->display_due_at }}"
                        data-provide="datepicker" 
                        data-date-container="#due_at"
                        data-date-autoclose="true"
                        data-date-format="M d, yyyy"
                        data-date-start-date="-1m"
                        data-date-end-date="+6m"
                        data-date-today-highlight="true"
                        >
                        <div class="invalid-feedback">
                            Due date is required
                        </div>
                    </div>
                    <div class="col-xl-6" id="tracking_number">
                        <label class="form-label">Tracking number</label>
                        <input type="text" class="form-control" name="tracking_number" >
                        <div class="invalid-feedback">
                           Tracking number is required
                        </div>
                    </div>
                </div>

                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="ship_order">Dispatch order</button>
            </form>

        </div>
    </div>
</div>


{{-- delivered --}}

{{-- <div class="mt-lg-0 rounded @if ($order->status != 4) d-none @endif">
    <div class="card border">
        <div class="card-body">
            <form name="receive-order-form"  class="needs-validation" novalidate
                action="{{ route('sale-orders.delivered', $order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-xl-6" id="delivered_at">
                        <label class="form-label">Delivery date</label>
                        <input type="text" class="form-control" name="delivered_at"
                            value="{{ $order->display_delivered_at }}" 
                            data-provide="datepicker" 
                            data-date-autoclose="true"
                            data-date-container="#delivered_at" 
                            data-date-format="M d, yyyy" 
                            required 
                            data-date-start-date="-1m"
                            data-date-end-date="+6m"
                            data-date-today-highlight="true">
                    </div>
                </div>
            
                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="ship_order">Deliver order</button>
            </form>

        </div>
    </div>
</div> --}}

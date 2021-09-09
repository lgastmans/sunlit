<div class=" mt-4 mt-lg-0 rounded @if ($purchase_order->status > 2) d-none @endif">
    <div class="card mt-4 border">
        <div class="card-body">
            <form name="place-order-form"
                action="{{ route('purchase-orders.confirm', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <div class="mb-3 position-relative" id="confirmed_at">
                    <label class="form-label">Confirmed date</label>
                    <input type="text" class="form-control" name="confirmed_at"
                        value="{{ $purchase_order->display_confirmed_at }}" data-provide="datepicker"
                        data-date-container="#confirmed_at" data-date-format="d-M-yyyy">
                </div>
                <button class="col-lg-12 text-center btn btn-danger" type="submit"
                    name="confirm_order">Confirm order</button>
            </form>

        </div>
    </div>
</div>
<div class=" mt-4 mt-lg-0 rounded @if ($purchase_order->status > 2) d-none @else d-none @endif">
    <div class="card mt-4 border">
        <div class="card-body">
            <form name="place-order-form"
                action="{{ route('purchase-orders.update', $purchase_order->id) }}" method="POST">
                @csrf()
                @method('PUT')
                <input type="hidden" name="field" value="ordered_at">
                <div class="mb-3 position-relative" id="ordered_at">
                    <label class="form-label">Ordered date</label>
                    <input type="text" class="form-control" name="ordered_at"
                        value="{{ $purchase_order->display_ordered_at }}" data-provide="datepicker"
                        data-date-container="#ordered_at" data-date-format="d-M-yyyy">
                </div>
                <button class="col-lg-12 text-center btn btn-warning" type="submit"
                    name="place_order">Update order</button>
            </form>

        </div>
    </div>
</div>
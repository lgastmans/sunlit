<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Order log</h4>

                <div class="table-responsive">
                    <table class="table table-sm table-centered mb-0">
                        <thead>
                            <th>Date</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tbody>
                            @if ($purchase_order->status >= 7)
                            <tr>
                                <td>{{ $purchase_order->display_received_at }}</td><td>The order has been received</td>
                            </tr>
                            @endif
                            @if ($purchase_order->status >= 6)
                            <tr>
                                <td>{{ $purchase_order->display_cleared_at }}</td><td>The order is cleared from customs</td>
                            </tr>
                            @endif
                            @if ($purchase_order->status >= 5)
                            <tr>
                                <td>{{ $purchase_order->display_customs_at }}</td><td>The order is at Customs, Bill of Entry <b>#{{ $purchase_order->boe_number }}</b></td>
                            </tr>
                            @endif
                            @if ($purchase_order->status >= 4)
                            <tr>
                                <td>{{ $purchase_order->display_shipped_at }}</td><td>The order has been shipped via <b>{{ $purchase_order->courier }}</b>, <b>#{{ $purchase_order->tracking_number }}</b>, and is expected on <b>{{ $purchase_order->display_due_at }}</b></td>
                            </tr>
                            @endif
                            @if ($purchase_order->status >= 3)
                            <tr>
                                <td>{{ $purchase_order->display_confirmed_at }}</td><td>The order has been confirmed by <b>{{ $purchase_order->supplier->company }}</b></td>
                            </tr>
                            @endif
                            @if ($purchase_order->status >= 2)
                            <tr>
                                <td>{{ $purchase_order->display_ordered_at }}</td><td>The order has been placed by <b>{{ $purchase_order->user->display_name }}</b></td>
                            </tr>
                            @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
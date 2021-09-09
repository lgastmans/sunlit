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
                        @if ($purchase_order->status >= 2)
                        <tr>
                            <td>{{ $purchase_order->display_ordered_at }}</td><td>The order has been placed</td>
                        </tr>
                        @endif
                        @if ($purchase_order->status >= 3)
                        <tr>
                            <td>{{ $purchase_order->display_confirmed_at }}</td><td>The order has been confirmed by <b>{{ $purchase_order->supplier->company }}</b></td>
                        </tr>
                        @endif
                        @if ($purchase_order->status >= 4)
                        <tr>
                            <td>{{ $purchase_order->display_shipped_at }}</td><td>The order has been shipped via <b>{{ $purchase_order->courier }}</b>, <b>#{{ $purchase_order->tracking_number }}</b></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
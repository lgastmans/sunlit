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
                            <td>{{ $purchase_order->display_confirmed_at }}</td><td>The order has been confirmed by {{ $purchase_order->supplier->company }}</td>
                        </tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
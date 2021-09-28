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
                            @if ($order->status >= 7)
                            <tr>
                                <td>{{ $order->display_delivered_at }}</td><td>The order has been delivered</td>
                            </tr>
                            @endif
                          
                            @if ($order->status >= 4)
                            <tr>
                                <td>{{ $order->display_shipped_at }}</td><td>The order has been shipped via <b>{{ $order->courier }}</b>, <b>#{{ $order->tracking_number }}</b>, and is expected on <b>{{ $order->display_due_at }}</b></td>
                            </tr>
                            @endif
                            @if ($order->status >= 3)
                            <tr>
                                <td>{{ $order->display_confirmed_at }}</td><td>The order has been confirmed by <b>{{ $order->dealer->company }}</b></td>
                            </tr>
                            @endif
                            @if ($order->status >= 2)
                            <tr>
                                <td>{{ $order->display_ordered_at }}</td><td>The order has been placed by <b>{{ $order->user->display_name }}</b></td>
                            </tr>
                            @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
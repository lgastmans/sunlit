<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-3">Invoice log</h4>

            <div class="table-responsive">
                <table class="table table-sm table-centered mb-0">
                    <thead>
                        <th>Date</th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        @if ($invoice->status >= 7)
                        <tr>
                            <td>{{ $invoice->display_received_at }}</td><td>The order has been received</td>
                        </tr>
                        @endif
                        @if ($invoice->status >= 6)
                        <tr>
                            <td>{{ $invoice->display_cleared_at }}</td><td>The order is cleared from customs</td>
                        </tr>
                        @endif
                        @if ($invoice->status >= 5)
                        <tr>
                            <td>{{ $invoice->display_customs_at }}</td><td>The order is at Customs, Bill of Entry <b>#{{ $invoice->boe_number }}</b></td>
                        </tr>
                        @endif
                        @if ($invoice->status >= 4)
                        <tr>
                            <td>{{ $invoice->display_shipped_at }}</td><td>The order has been shipped via <b>{{ $invoice->courier }}</b>, <b>#{{ $invoice->tracking_number }}</b>, and is expected on <b>{{ $invoice->display_due_at }}</b></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

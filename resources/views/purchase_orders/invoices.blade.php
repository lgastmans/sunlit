<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-3">Existing invoices against purchase order</h4>
            <div class="table-responsive">
                @if (count($purchase_order->invoices) == 0)
                    No invoice.
                @else
                    <table class="table table-sm table-centered mb-0">
                        <thead>
                            <th>Date</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            @foreach ($purchase_order->invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->display_shipped_at }}</td>
                                    <td><strong>{!! $invoice->display_status !!}  #{{ $invoice->invoice_number }}</strong> {{ $invoice->user->display_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

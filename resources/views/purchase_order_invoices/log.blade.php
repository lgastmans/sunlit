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
                        @foreach($activities as $activity)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($activity->updated_at)->toFormattedDateString() }}
                                </td>
                                <td>
                                    {!! $activity->description.' by <b>'.$activity->causer->name.'</b>' !!}
                                </td>
                            </tr>
                        @endforeach
                        
{{--                         @if ($invoice->status >= 8)
                        <tr>
                            <td>{{ $invoice->display_paid_at }}</td><td>The invoice has been paid, <b>#{{ $invoice->payment_reference }} / <b>{{ __('app.currency_symbol_inr')}}{{ $invoice->paid_exchange_rate }}</b></b></td>
                        </tr>
                        @endif
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
                            <td>{{ $invoice->display_shipped_at }}</td><td>The order has been shipped</b></td>
                        </tr>
                        @endif
 --}}                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-3">Payment & Delivery Terms</h4>
            <textarea class="form-control" name="purchase_order_invoice_terms" id="poi_terms">{!! $invoice->payment_terms !!}</textarea>
        </div>
    </div>
</div>

@push('page-scripts')

    <script>

        var text_changed = false;

        ClassicEditor
            .create( document.querySelector( '#poi_terms' ) )
            .then( editor => {
                window.editor = editor;

                detectTextChanges(editor);
                detectFocusOut(editor);
                    // editor.on("blur", function(){
                    //     console.log("hello world");
                    // });
            })
            .catch( error => {
                console.error( error );
            });

            function detectFocusOut(editor) {
                editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                    if (!isFocused && text_changed) {
                        text_changed = false;

                        console.log('here'+editor.getData());

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        var route = '{{ route("purchase-order-invoices.update", ":id") }}';
                        route = route.replace(':id', $('#purchase-order-invoice-id').val());

                        var field_name = 'payment_terms';
                        var field_value = editor.getData();

                        $.ajax({
                            type: 'POST',
                            url: route,
                            dataType: 'json',
                            data: { 
                                'value': field_value,
                                'field': field_name, 
                                'item': false,
                                '_method': 'PUT'
                            },
                            success : function(result){
                                //console.log(result);
                                
                                // $.NotificationApp.send("Success","Transport Charges saved","top-right","","success")
                            }
                        });
                    }
                });
            }

            function detectTextChanges(editor) {
                editor.model.document.on('change:data', () => {
                    text_changed = true;
                });
            }

    </script>

@endpush
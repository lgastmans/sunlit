<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Proforma Invoice log</h4>

                <div class="table-responsive">
                    <table class="table table-sm table-centered mb-0" id="table-activity-log">
                        <thead>
                            <th>Date</th>
                            <th>&nbsp;</th>
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


{{--                             @if ($order->status >= 7)
                            <tr>
                                <td>{{ $order->display_delivered_at }}</td><td>The order has been delivered</td>
                            </tr>
                            @endif
                          
                            @if ($order->status >= 4)
                            <tr>
                                <td>{{ $order->display_dispatched_at }}</td><td>The order has been dispatched via <b>{{ $order->courier }}</b></td>
                            </tr>
                            @endif
                            @if ($order->status >= 3)
                            <tr>
                                <td>{{ $order->display_booked_at }}</td><td>The order has been booked by <b>{{ $order->dealer->company }}</b></td>
                            </tr>
                            @endif
                            @if ($order->status >= 2)
                            <tr>
                                <td>{{ $order->display_blocked_at }}</td><td>The order has been blocked by <b>{{ $order->user->display_name }}</b></td>
                            </tr>
                            @endif
 --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">

                <h4 class="header-title mb-3">Payments</h4>

                <div class="row">
                    <table id="invoice_payments" class="table table-striped table-bordered table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <hr />

                <button type="button" class="col-lg-4 text-center btn btn-success" id="btn-add">
                    Add Payment
                </button>

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Payment & Delivery Terms</h4>
                <textarea class="form-control" name="sale_order_terms" id="so_terms">{!! $order->payment_terms !!}</textarea>
            </div>
        </div>
    </div>

</div>


<div id="payment-modal-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete-modalLabelOrder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-success">
                <h4 class="modal-title" id="delete-modalLabelOrder">Add Payment for #{{ $order->order_number }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">

                <form>
                    @csrf()
                    <div class="form-group">
                        <label for="amount" class="col-sm-2 control-label">Amount</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="amount" placeholder="Amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference" class="col-sm-2 control-label">Reference</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="reference" placeholder="Reference">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="payment-date" class="col-sm-2 control-label">Date</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="payment-date" placeholder="Date">
                        </div>
                    </div>
                </form>
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.modal_close') }}</button>
                <button type="button" class="btn btn-success" id="btn-payment-save">{{ __('app.save_profile') }}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



@push('page-scripts')

    <script>

        var text_changed = false;
        var payment_id = null;
        var url = '';

        ClassicEditor
            .create( document.querySelector( '#so_terms' ) )
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

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var route = globalSettings.sale_order_update;
                    route = route.replace(':id', $('#sale-order-id').val());

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

        var paymentsTable = $('#invoice_payments').DataTable({
            scrollY         : '40vh',
            scrollCollapse  : true,
            paging          : false,
            searching       : false,
            fixedColumns    : true,
            ajax            : {
                url   : "{{ route('sale-order-payments.datatables') }}",
                "data": function ( d ) {
                    d.filter_sale_order_id = {{ $order->id }};
                },
            },
            columns: [
                { data: "id", visible: false, 'orderable': true },
                { data: "payment_date", 'orderable': false },
                { data: "amount", 'orderable': false },
                { data: "payment_reference", 'orderable': false },
                { 
                    targets: -1, 
                    data: null, 
                    width: '10px',
                    'orderable': false,
                    defaultContent: '<a href="javascript:void(0);" class="action-icon" id="btn-edit"> <i class="mdi mdi-pencil"></i></a>',
                    
                },
                { 
                    targets: -1, 
                    data: null, 
                    width: '10px',
                    'orderable': false,
                    defaultContent: '<a href="javascript:void(0);" class="action-icon" id="btn-delete"> <i class="mdi mdi-delete"></i></a>',
                }

            ],
            oLanguage       : {
                "sInfo": "", //"_TOTAL_ entries",
                "sInfoEmpty": "No entries",
                "sEmptyTable": "No payments received",
            }
        });

        
        /*
            add a payment
        */
        $('#btn-add').on('click', function () {

            payment_id = null;
            
            $('#amount').val('');
            $('#reference').val('');
            $('#payment-date').val('');

            $('#payment-modal-order').modal('show');

        });

        /*
            edit a payment
        */
        $('#invoice_payments tbody').on('click', '[id*=btn-edit]', function () {

            var data = paymentsTable.row($(this).parents('tr')).data();

            var paymentDate = new Date(data.payment_date).toISOString().slice(0, 10);
            var paymentAmount = data.amount;
            paymentAmount = Number(paymentAmount.replace(/[^0-9.-]+/g,""));

            payment_id = data.id;
            $('#amount').val(paymentAmount);
            $('#reference').val(data.payment_reference);
            $('#payment-date').val(data.payment_date);

            $('#payment-modal-order').modal('show');

        });

        /*
            delete a payment
        */
        $('#invoice_payments tbody').on('click', '[id*=btn-delete]', function () {

            var data = paymentsTable.row($(this).parents('tr')).data();

            payment_id = data.id;

            var route = '{{  route("sale-order-payments.delete", ":id") }}';
            route = route.replace(':id', payment_id);

            if (confirm("Are you sure?")) {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    method  : "POST",
                    url     : route,
                    data    : { 
                        _method: "DELETE",
                        payment_id : payment_id 
                    }
                })
                .done ( function( msg ) {

                    paymentsTable.ajax.reload();
                    $("<tr><td>"+msg.log_date+"</td><td>"+msg.log_text+"</td></tr>").prependTo("#table-activity-log > tbody");

                });
            }
        });

        /*
            save the payment details
        */
        $('#btn-payment-save').click(function() {

            var amount = $("#amount").val();
            var reference = $("#reference").val();
            var payment_date = $('#payment-date').val();

            if (isNaN(parseFloat(amount)))
            {
                $.NotificationApp.send("Error","Invalid Amount","top-right","","error")
                return false;
            }

            if (parseFloat(amount) === 0)
            {
                $.NotificationApp.send("Error","Amount cannot be zero","top-right","","error")
                return false;
            }

            if(!moment("05/22/2012", 'MM/DD/YYYY',true).isValid())
            {
                $.NotificationApp.send("Error","Incorrect date","top-right","","error")
                return false;
            }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: "POST",
                url: "{{ route('sale-order-payments.store') }}",
                data: {
                    payment_id: payment_id,
                    sale_order_id: $("#sale-order-id").val(), 
                    amount: amount,
                    reference: reference,
                    paid_at: payment_date
                }
            }).done(function (msg) {
                
                paymentsTable.ajax.reload();
                
                $('#payment-modal-order').modal('hide');

                $("<tr><td>"+msg.log_date+"</td><td>"+msg.log_text+"</td></tr>").prependTo("#table-activity-log > tbody");
            });

        });        
    </script>

@endpush
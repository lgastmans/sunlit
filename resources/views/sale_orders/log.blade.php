<div class="row">
    <div class="col-lg-6">
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
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Payment & Delivery Terms</h4>
                <textarea class="form-control" name="sale_order_terms" id="so_terms">{!! $order->payment_terms !!}</textarea>
            </div>
        </div>
    </div>

</div>


@push('page-scripts')

    <script>

        var text_changed = false;

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

        
    </script>

@endpush
@extends('layouts.app')

@section('title')
    @parent() | SO #{{ $order->order_number }}
@endsection

@section('page-title')
    Sale Order #{{ $order->order_number}}
@endsection

@section('content')

@include('sale_orders.steps')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Items from Order #{{ $order->order_number }}</h4>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Tax</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->part_number }}</td>
                                <td>{{ $item->quantity_ordered }}</td>
                                <td>{{ __('app.currency_symbol_inr')}}{{ number_format($item->selling_price,2) }}</td>
                                <td>{{ number_format($item->tax,2) }}%</td>
                                <td>{{ __('app.currency_symbol_inr')}}{{ number_format($item->total_price,2) }}</td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
        </div>


    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">

                <input type="hidden" name="sale_order_id" id="sale-order-id" value="{{ $order->id }}">

                <h4 class="header-title mb-3">Order Summary #{{ $order->order_number }}</h4>

                <a class="btn btn-success" href="{{ route('sale-orders.proforma-pdf', $order->order_number) }}" role="button" target="_blank">Export to PDF</a>

                <a class="btn btn-success" href="{{ route('sale-orders.proforma', $order->order_number) }}" role="button" target="_blank">View Proforma</a>


                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Order Total :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="grand-total">{{ $order->amount }}</span>
                                </td>
                            </tr>
                          
                         
                            @if ($order->status >= "5")
                                
                                <tr>
                                    <td>Transport Charges: </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="bank-transport">{{ $order->transport_charges }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Cost :</th>
                                    <th>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="landed-cost">{{ $order->amount + $order->transport_charges }}</span>
                                    </th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    
                </div>
                <!-- end table-responsive -->
            </div>
        </div>
    </div> <!-- end col -->
</div>
<div class="row">
    <div class="col-lg-8">
        @include('sale_orders.info_cards')
    </div>
    <div class="col-lg-4">
        @include('sale_orders.status_update')
    </div>
</div>
<div class="row">    
    @include('sale_orders.log')
</div>
@endsection

@section('page-scripts')

    <script>

         $(document).ready(function () {
            "use strict";

            $("#btn_transport_charges").on('click', function(e) {

                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                }); 

                var route = '{{ route("sale-orders.update", ":id") }}';
                route = route.replace(':id', $('#sale-order-id').val());

                var freight = 123; //$("#freight").val();
                console.log('here ' + freight );

                $.ajax({
                        type: 'POST',
                        url: route,
                        dataType: 'json',
                        data: { 
                            'value' : freight, 
                            'field': 'transport_charges', 
                            'item': false,
                            '_method': 'PUT'
                        },
                        success : function(result){
                            console.log(result);

                            $.NotificationApp.send("Success","Transport Charges saved","top-right","","success")

                        }
                });        

            });
        });

    </script>

@endsection
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

                {{-- <a class="btn btn-success" href="{{ route('sale-orders.proforma', $order->order_number) }}" role="button" target="_blank">View Proforma</a> --}}


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
                          
                         
                            @if ($order->status >= 3)
                                <tr>
                                    <td>Transport Charges: </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="transport-charges">{{ $order->transport_total ?? '0.00' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Cost :</th>
                                    <th>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="total-cost">{{ $order->total }}</span>
                                    </th>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                    
                </div>
                <!-- end table-responsive -->
            </div>
        </div> <!-- card -->
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

@if ($order->status >= 2 && $order->status <= 3)
    <div class="row">
        <div class="col-lg-8">
        </div>
        <div class="col-lg-4">
            <div class="mt-4 mt-lg-0 rounded">
                <div class="card mt-4 border">
                    <div class="card-body">
                        <button id="btn_delete_order" class="col-lg-12 text-center btn btn-danger" type="submit" name="delete_order" data-bs-toggle="modal" data-bs-target="#delete-modal-order"><i class="mdi mdi-delete"></i> Delete order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

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

                var freight = $("#freight").val();

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

                            $(" #transport-charges ").html(result.transport_charges);
                            $(" #total-cost ").html(result.total);

                            $.NotificationApp.send("Success","Transport Charges saved","top-right","","success")

                        }
                });        

            }); // transport charges


            $("#btn_delete_order").on('click', function(e) {

                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                }); 

                var route = '{{ route("sale-orders.delete", ":id") }}';
                route = route.replace(':id', $('#sale-order-id').val());

                $.ajax({
                        type: 'DELETE',
                        url: route,
                        dataType: 'json',
                        success : function(result){
                            console.log(result);

                            window.location.href = '{{ route("sale-orders") }}';
                        }
                });        

            });


        }); //document ready

    </script>

@endsection
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
                                <th class="col-5">Product</th>
                                <th class="col-2">Price</th>
                                <th class="col-1">Quantity</th>
                                <th class="col-1">Tax</th>
                                <th class="col-2">Total</th>
                                <th class="col-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @if  ($order->status == 4)
                                     <tr>
                                        <td>{{ $item->product->part_number }}</td>
                                        <td>{{ $item->quantity_ordered }}</td>
                                        <td>{{ __('app.currency_symbol_inr')}}{{ number_format($item->selling_price,2) }}</td>
                                        <td>{{ number_format($item->tax,2) }}%</td>
                                        <td>{{ __('app.currency_symbol_inr')}}{{ number_format($item->total_price,2) }}</td>
                                    </tr>
                                @else

                                    <tr class="item" data-id="{{$item->id}}" data-product-id="{{ $item->product->id }}">

                                        <td>
                                            <p class="m-0 d-inline-block align-middle font-16">
                                                <a href="javascript:void(0);"
                                                    class="text-body product-name">{{ $item->product->part_number }}</a>
                                                <br>
                                                <small class="me-2"><b>Description:</b> <span class="product-note">{{  $item->product->note }}</span> </small>
                                            </p>
                                        </td>
                                        <td>
                                            <div class="input-group flex-nowrap">
                                                <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                                <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->selling_price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->selling_price }}">
                                            </div>
                                        </td>
                                        <td>
                                            <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity_ordered }}" class="editable-field form-control" data-value="{{ $item->quantity_ordered }}" data-field="quantity" data-item="{{ $item->id }}"
                                                placeholder="Qty" style="width: 120px;">
                                        </td>
                                        <td>
                                            <span id="item-tax-{{ $item->id }}">@if ($item->tax){{ $item->tax }}@else 0.00 @endif%</span>
                                        </td>
                                        <td>
                                            <span>{{ __('app.currency_symbol_inr')}}</span>
                                            <span id="item-total-{{ $item->id }}" class="item-total">{{ $item->total_price }}</span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="action-icon" id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>
                                        </td>
                                    </tr>

                                @endif
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
                        <button id="{{ $order->id }}" class="col-lg-12 text-center btn btn-danger" type="submit" name="delete_order" data-bs-toggle="modal" data-bs-target="#delete-modal-order"><i class="mdi mdi-delete"></i> Delete order</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">    
    @include('sale_orders.log')
</div>

<div id="delete-modal-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete-modalLabelOrder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-order-form" action="" method="POST">
                @method("DELETE")
                @csrf()
                <div class="modal-header modal-colored-header bg-danger">
                    <h4 class="modal-title" id="delete-modalLabelOrder">Delete Sale Order {{ $order->order_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    {{ __('app.delete_confirm', ['field' => "Sale Order" ]) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.modal_close') }}</button>
                    <button type="button" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('delete-order-form').submit();">{{ __('app.modal_delete') }}</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<x-modal-confirm type="danger" target="item"></x-modal-confirm>

@endsection

@section('page-scripts')

    <script>
        let globalSettings = 
        {
            sale_order_update : '{{ route("sale-orders.update", ":id") }}',
            ajax_dealers : '{{route("ajax.dealers")}}',
            product_json : '{{ route("product.json", [":id", ":warehouse_id"]) }}',
            sale_order_items_update : '{{ route("sale-orders-items.update", ":id") }}',
            sale_order_items_delete : '{{ route("sale-orders-items.delete", ":id") }}',
            sale_order_delete : '{{ route("sale-orders.delete", ":id") }}',
            product_route : '{{ route("ajax.products.warehouse", [":warehouse_id"]) }}',
            inr_symbol : '{{ __("app.currency_symbol_inr")}}'
        };

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

        }); //document ready

    </script>

    <script src="{{ asset('js/pages/sale_order_cart.js') }}"></script>

@endsection
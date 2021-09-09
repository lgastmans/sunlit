@extends('layouts.app')

@section('page-title')
    Purchase Orders #{{ $purchase_order->order_number}}
@endsection

@section('content')

@include('purchase_orders.steps')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Items from Order #{{ $purchase_order->order_number }}</h4>

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
                            @foreach($purchase_order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->quantity_ordered }}</td>
                                <td>${{ number_format($item->selling_price,2) }}</td>
                                <td>{{ number_format($item->tax,2) }}%</td>
                                <td>${{ number_format($item->total_price,2) }}</td>
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
                <h4 class="header-title mb-3">Order Summary #{{ $purchase_order->order_number }}</h4>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Grand Total :</td>
                                <td>${{ $purchase_order->amount_usd }}</td>
                            </tr>
                            <tr>
                                <td>Shipping Charge :</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Estimated Tax : </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Total :</th>
                                <th>$1683.22</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
        </div>
        @include('purchase_orders.status_update')
    </div> <!-- end col -->
</div>

    @include('purchase_orders.info_cards')
    @include('purchase_orders.log')

@endsection

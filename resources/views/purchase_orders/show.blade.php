@extends('layouts.app')

@section('page-title')
    Purchase Orders #{{ $purchase_order->order_number}}
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 col-sm-11">
                <div class="horizontal-steps mt-4 mb-4 pb-5" id="tooltip-container">
                    <div class="horizontal-steps-content">
                        <div class="step-item @if ($purchase_order->status == 2) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_ordered_at }}">Ordered</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 3) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->display_confirmed_at }}">Confirmed</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 4) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->shipped_at }}">Shipped</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 5) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->customs_at }}">Customs</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 6) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->cleared_at }}">Cleared</span>
                        </div>
                        <div class="step-item @if ($purchase_order->status == 7) current @endif">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $purchase_order->received_at }}">Received</span>
                        </div>
                    </div>
                    <div class="process-line" style="width: {{ $purchase_order->status*7 }}%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
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

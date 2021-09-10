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
                                <td>{{ __('app.currency_symbol_usd')}}{{ number_format($item->selling_price,2) }}</td>
                                <td>{{ number_format($item->tax,2) }}%</td>
                                <td>{{ __('app.currency_symbol_usd')}}{{ number_format($item->total_price,2) }}</td>
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
                        <tbody>
                            <tr>
                                <td>Grand Total :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_usd')}}</span>
                                    <span id="grand-total">{{ $purchase_order->amount_usd }}</span>
                                </td>
                            </tr>
                          
                            <tr>
                                <td>Exchange Rate :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="echange-rate">{{ $purchase_order->order_exchange_rate }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Amount in {{ __('app.currency_symbol_inr')}} : </td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="amount-inr">{{ $purchase_order->amount_inr }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total :</th>
                                <th>
                                    <span>{{ __('app.currency_symbol_usd')}}</span>
                                    <span id="total">{{ $purchase_order->amount_usd }}</span>
                                </th>
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

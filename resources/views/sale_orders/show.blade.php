@extends('layouts.app')

@section('title')
    @parent() | SO #{{ $order->order_number }}
@endsection

@section('page-title')
    Sale Orders #{{ $order->order_number}}
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
                                <td>{{ $item->product->name }}</td>
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
                <h4 class="header-title mb-3">Order Summary #{{ $order->order_number }}</h4>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Order Total :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_usd')}}</span>
                                    <span id="grand-total">{{ $order->amount }}</span>
                                </td>
                            </tr>
                          
                         
                            @if ($order->status >= "5")
                                <tr>
                                    <td>Customs Duty : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="customs-duty">{{ $order->customs_duty }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Social Welfare Surcharge : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="social-welfare-surcharge">{{ $order->social_welfare_surcharge }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>IGST : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="igst">{{ $order->igst }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bank & Transport : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="bank-transport">{{ $order->bank_and_transport_charges }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Landed Cost :</th>
                                    <th>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="landed-cost">{{ $order->landed_cost }}</span>
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
       
    
</script>

@endsection
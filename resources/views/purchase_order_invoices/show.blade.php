@extends('layouts.app')

@section('title')
    @parent() | Invoice #{{ $invoice->invoice_number }}
@endsection

@section('page-title')
    Invoice #{{ $invoice->invoice_number}} 
@endsection

@section('content')

@include('purchase_order_invoices.steps')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <h4 class="col-lg-6 header-title mb-3">Items from Invoice #{{ $invoice->invoice_number }}</h4>
                    <a href="{{ route("purchase-orders.show", $invoice->purchase_order->order_number) }}" class="offset-lg-4 col-lg-2">
                        <button class="text-center btn btn-sm btn-info" type="submit">View Purchase Order</button>
                    </a>
                </div>
                 
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Quantity Shipped</th>
                                <th>Price</th>
                                <th class="d-none">Tax</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->product->part_number }}<br>
                                    <small class="me-2">
                                        <b>Customs duty:</b> <span class="customs-duty">{{  $item->product->category->customs_duty }}%</span>
                                         - <b>Surcharge:</b> <span class="social-welfare">{{  $item->product->category->social_welfare_surcharge }}%</span>
                                         - <b>IGST:</b> <span class="igst">{{  $item->product->category->igst }}%</span>
                                    </small>
                                </td>
                                <td>{{ $item->quantity_shipped }}</td>
                                <td>{{ __('app.currency_symbol_usd')}}{{ number_format($item->buying_price,2) }}</td>
                                <td class="d-none">{{ number_format($item->tax,2) }}%</td>
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
                <h4 class="header-title mb-3">Invoice Summary #{{ $invoice->invoice_number }}</h4>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Invoice Total :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_usd')}}</span>
                                    <span id="grand-total">{{ $invoice->amount_usd }}</span>
                                </td>
                            </tr>
                          
                            <tr>
                                <td>Exchange Rate <abbr title="Supplier">(S</abbr>/<abbr title="Customs">C</abbr>) :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="order-echange-rate">{{ $invoice->order_exchange_rate }}</span>
                                    /
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="customs-echange-rate">{{ $invoice->customs_exchange_rate }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Amount : </td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="amount-inr">{{ $invoice->amount_inr }}</span>
                                </td>
                            </tr>
                            @if ($invoice->status >= "5")
                                <tr>
                                    <td>Customs Duty : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="customs-duty">{{ $invoice->customs_duty }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Social Welfare Surcharge : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="social-welfare-surcharge">{{ $invoice->social_welfare_surcharge }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>IGST : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="igst">{{ $invoice->igst }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Landed Cost :</th>
                                    <th>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="landed-cost">{{ $invoice->landed_cost }}</span>
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
        @include('purchase_order_invoices.info_cards')
    </div>
    <div class="col-lg-4">
        @include('purchase_order_invoices.status_update')
    </div>
</div>
<div class="row">    
    @include('purchase_order_invoices.log')
</div>
@endsection

@section('page-scripts')
 <script>
       
    function calculateCharges(from){
        var settings = Array;
        settings['customs_duty'] = {{ Setting::get('purchase_order.customs_duty')/100 }};
        settings['igst'] = {{ Setting::get('purchase_order.igst')/100 }};
        settings['social_welfare_surcharge'] = {{ Setting::get('purchase_order.social_welfare_surcharge')/100 }};


        if (from == "rate"){
            var customs_exchange_rate = parseFloat($('#customs_exchange_rate').val());
            var total_order_customs = $('#grand-total').html() * customs_exchange_rate;
            $('#customs_amount').val(total_order_customs.toFixed(2));
        }
        
        if (from == "amount"){
            var total_order_customs = parseFloat($('#customs_amount').val());
            var customs_exchange_rate = total_order_customs / parseFloat($('#grand-total').html());
            $('#customs_exchange_rate').val(customs_exchange_rate.toFixed(2));
        }

        var order_amount_inr = parseFloat($('#amount-inr').html());            
        var customs_duty = total_order_customs * settings['customs_duty'];
        var social_welfare_surchage = customs_duty * settings['social_welfare_surcharge'];
        var igst = (order_amount_inr + customs_duty + social_welfare_surchage) * settings['igst'];
        var landed_cost = order_amount_inr + customs_duty + social_welfare_surchage;


        $('#customs-echange-rate').html(customs_exchange_rate.toFixed(2));
        $('#customs-duty').html(customs_duty.toFixed(2));
        $('#social-welfare-surcharge').html(social_welfare_surchage.toFixed(2));
        $('#igst').html(igst.toFixed(2));
        $('#landed-cost').html(landed_cost.toFixed(2));

    }
 

    $(document).ready(function () {
        "use strict";
        
        $('#customs_exchange_rate').on('change', function(e){
            calculateCharges('rate');            
        });

        $('#customs_amount').on('change', function(e){
            calculateCharges('amount');            
        });
    }); 
</script>

@endsection
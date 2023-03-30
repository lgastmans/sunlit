@extends('layouts.app')

@section('title')
    @parent() | Invoice #{{ $invoice->invoice_number }}
@endsection

@section('page-title')
    {{ ($purchase_order->supplier->is_international ? 'International' : 'Domestic')}} Invoice #{{ $invoice->invoice_number}}  | <a href="{{ route("purchase-orders.show", $invoice->purchase_order->order_number_slug) }}">{{ $invoice->purchase_order->order_number }}</a>
@endsection

@section('content')

@include('purchase_order_invoices.steps')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <input type="hidden" name="purchase_order_invoice_id" id="purchase-order-invoice-id" value="{{ $invoice->id }}">
                    <h4 class="col-lg-6 col-xxl-6 header-title mb-3">Items from Invoice #{{ $invoice->invoice_number }}</h4>
                    <a href="{{ route("purchase-orders.show", $invoice->purchase_order->order_number_slug) }}" class="offset-lg-2 col-lg-4 offset-xxl-4 col-xxl-2">
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
                                <th class="d-none">CD/SWS/IGST</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->product->part_number }}
                                    @if ($purchase_order->supplier->is_international)
                                    <br>
                                    <small class="me-2">
                                        <b>Customs duty:</b> <span class="customs-duty" id="customs-duty-perc">{{  $item->charges['customs_duty'] }}%</span>
                                         - <b>Surcharge:</b> <span class="social-welfare" id="social-welfare-perc">{{  $item->charges['social_welfare_surcharge'] }}%</span>
                                         - <b>IGST:</b> <span class="igst" id="igst-perc">{{  $item->charges['igst'] }}%</span>
                                    </small>
                                    @endif
                                </td>
                                <td>{{ $item->quantity_shipped }}</td>
                                <td>{{ ($purchase_order->supplier->is_international) ? __('app.currency_symbol_usd') : __('app.currency_symbol_inr')}}{{ number_format($item->buying_price,2) }}</td>
                                <td class="d-none">
                                    <span class="product-customs-duty-usd" data-value="{{ $item->customs_duty }}">{{  number_format($item->customs_duty,2) }}</span>
                                    <span class="product-social-welfare-surcharge-usd" data-value="{{ $item->social_welfare_surcharge }}">{{  number_format($item->social_welfare_surcharge,2) }}</span>
                                    <span class="product-igst-usd" data-value="{{ $item->igst }}">{{  number_format($item->igst,2) }}</span>
                                </td>
                                <td>{{ ($purchase_order->supplier->is_international) ? __('app.currency_symbol_usd') : __('app.currency_symbol_inr')}}{{ number_format($item->total_price,2) }}</td>
                                <td>
                                    @if ($invoice->status <= "5")
                                        <a href="javascript:void(0);" class="action-icon" id="icon-refresh" name="{{ $item->id }}" title="reload the category percentages for this item"> <i class="mdi mdi-refresh"></i></a>
                                    @endif
                                </td>
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

                <a class="btn btn-success" href="{{ route('purchase-order-invoices.proforma-pdf', $invoice->invoice_number_slug) }}" role="button" target="_blank">Export to PDF</a>
                <a class="btn btn-success" href="{{ route('purchase-order-invoices.proforma', $invoice->invoice_number_slug) }}" role="button" target="_blank">View Proforma</a>


                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Invoice Total :</td>
                                <td>
                                    @if ($purchase_order->supplier->is_international)
                                        <span>{{ __('app.currency_symbol_usd') }}</span>
                                        <span id="amount-usd">{{ $invoice->amount_usd }}</span>
                                    @else
                                        <span>{{ __('app.currency_symbol_inr') }}</span>
                                        <span id="amount-usd">{{ $invoice->amount_inr }}</span>
                                    @endif
                                </td>
                            </tr>
                          
                            @if ($purchase_order->supplier->is_international)
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
                                    <td>Supplier Amount : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="amount-inr">{{ $invoice->amount_inr }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Customs Amount : </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="customs-amount-inr">@if ($invoice->customs_exchange_rate) {{ $invoice->amount_usd * $invoice->customs_exchange_rate }}@endif</span>
                                    </td>
                                </tr>
                            @endif

                            @if ($invoice->status >= "5")
                            
                                @if ($purchase_order->supplier->is_international)
                                    <tr>
                                        <td>Total Customs Duty : </td>
                                        <td>
                                            <span>{{ __('app.currency_symbol_inr')}}</span>
                                            <span id="customs-duty">{{ $invoice->customs_duty }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Social Welfare Surcharge : </td>
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
                                        <th>Landed Cost (rounded):</th>
                                        <th>
                                            <span>{{ __('app.currency_symbol_inr')}}</span>
                                            <span id="landed-cost">@php echo number_format($invoice->landed_cost,2); @endphp</span>
                                        </th>
                                    </tr>
                                @endif
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
        // var settings = Array;
        // settings['customs_duty'] = {{ Setting::get('purchase_order.customs_duty')/100 }};
        // settings['igst'] = {{ Setting::get('purchase_order.igst')/100 }};
        // settings['social_welfare_surcharge'] = {{ Setting::get('purchase_order.social_welfare_surcharge')/100 }};


        if (from == "rate"){
            var customs_exchange_rate = parseFloat($('#customs_exchange_rate').val());
            var total_order_customs = $('#amount-usd').html() * customs_exchange_rate;
            $('#customs_amount').val(total_order_customs.toFixed(2));
        }
        
        // if (from == "amount"){
        //     var total_order_customs = parseFloat($('#customs_amount').val());
        //     var customs_exchange_rate = total_order_customs / parseFloat($('#amount-usd').html());
        //     $('#customs_exchange_rate').val(customs_exchange_rate.toFixed(2));
        // }

        var order_amount_inr = parseFloat($('#amount-inr').html());
        var customs_amount_inr = parseFloat($('#amount-usd').html()) * customs_exchange_rate;
        var customs_duty_usd = 0;
        var social_welfare_surchage_usd = 0;
        var igst_usd = 0;

        $( ".product-customs-duty-usd" ).each(function( index ) {
            customs_duty_usd = customs_duty_usd + parseFloat($(this).attr('data-value'));
        });
        customs_duty_inr = customs_exchange_rate * customs_duty_usd;

        $( ".product-social-welfare-surcharge-usd" ).each(function( index ) {
            social_welfare_surchage_usd = social_welfare_surchage_usd + parseFloat($(this).attr('data-value'));
        });
        social_welfare_surcharge_inr = customs_exchange_rate * social_welfare_surchage_usd;

        $( ".product-igst-usd" ).each(function( index ) {
            igst_usd = igst_usd + parseFloat($(this).attr('data-value'));
        });
        igst_inr = customs_exchange_rate * igst_usd;
        
        //var landed_cost = customs_amount_inr + customs_duty_inr + social_welfare_surcharge_inr + igst_inr;
        var landed_cost = order_amount_inr + customs_duty_inr + social_welfare_surcharge_inr;


        $('#customs-echange-rate').html(customs_exchange_rate.toFixed(2));
        $('#customs-amount-inr').html(customs_amount_inr.toFixed(2));
        $('#customs-duty').html(customs_duty_inr.toFixed(2));
        $('#social-welfare-surcharge').html(social_welfare_surcharge_inr.toFixed(2));
        $('#igst').html(igst_inr.toFixed(2));
        $('#landed-cost').html(landed_cost.toFixed(2));


        $('#total_customs_duty_inr').val(customs_duty_inr);
        $('#total_social_welfare_surcharge_inr').val(social_welfare_surcharge_inr);
        $('#total_igst_inr').val(igst_inr);
        $('#landed_cost_inr').val(landed_cost);


    }
 

    $(document).ready(function () {
        "use strict";
        

        $("#icon-refresh").on("click", function(e){
            
            // var ids = $(this).attr('name').split('|');
            // var po_item_id = ids[0];
            // var product_id = ids[1];

            var po_item_id = $(this).attr('name');

            console.log(po_item_id);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            }); 

            var route = '{{ route("ajax.category", ":id") }}';
            route = route.replace(':id', po_item_id);
            
            $.ajax({
                type: 'GET',
                url: route,
                dataType: 'json',
                data: { 
                    'po_item_id': po_item_id,
                },
                success : function(result){
                    console.log(result);

                    $("#customs-duty-perc").text(result.customs_duty+'%');
                    $("#social-welfare-perc").text(result.social_welfare_surcharge+'%');
                    $("#igst-perc").text(result.igst+'%');

                }
            });
        });


        $('#customs_exchange_rate').on('change', function(e){
            calculateCharges('rate');            
        });

        // $('#customs_amount').on('change', function(e){
        //     calculateCharges('amount');            
        // });
    }); 
</script>

@endsection
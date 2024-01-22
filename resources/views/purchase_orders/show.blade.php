@extends('layouts.app')

@section('title')
    @parent() | PO #{{ $purchase_order->order_number }}
@endsection

@section('page-title')
    {{ ($purchase_order->supplier->is_international ? 'International' : 'Domestic')}} Purchase Order #{{ $purchase_order->order_number}}
@endsection

@section('content')

@include('purchase_orders.steps')

<style>
    #table-summary tr > td {
      padding-top:3px !important;
      padding-bottom:3px !important;
      padding-right:20px;   
    }    
</style>

<input type="hidden" id="is_international" value="{{ ($purchase_order->supplier->is_international) ? 'Y' : 'N' }}" >

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
                                <th style="text-align: center;">Quantity<br>Confirmed</th>
                                <th style="text-align: center;">Quantity<br>Invoice</th>
                                <th style="text-align: center;">Quantity<br>Received</th>
                                <th style="text-align: center;">Price</th>
                                <th class="d-none">Tax</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase_order->items as $item)
                            
                            <tr class="item" data-id="{{$item->id}}" data-product-id="{{ $item->product->id }}">
                                <td>{{ $item->product->part_number }}</td>
                                <td style="text-align: right;">
                                    <div class="input-group flex-nowrap">
                                        <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity_confirmed }}" class="editable-field form-control" data-value="{{ $item->quantity_confirmed }}" data-field="quantity" data-item="{{ $item->id }}" placeholder="Qty" style="width: 60px;">
                                    </div>
                                    <div class="invalid-feedback">
                                        Quantity cannot be less than Quantity Received
                                    </div>                                     
                                </td>
                                
                                <td style="text-align:center;">
                                    @if (!empty($shipped[$item->product_id]))
                                        @if ($item->quantity_confirmed <= $shipped[$item->product_id])
                                            <span class="badge bg-success">Nothing to receive</span>
                                        @else 
                                            <div class="input-group flex-nowrap">
                                                <input id="item-invoice-{{$item->id}}" data-max="{{ $item->quantity_confirmed - $shipped[$item->product_id] }}" class="form-control input-sm quantity_shipped" type="text" placeholder="{{ $item->quantity_confirmed -  $shipped[$item->product_id] }}" size="3" name="quantity_shipped" data-product="{{ $item->product_id }}">
                                            </div>
                                            <div class="invalid-feedback" id="item-invoice-err-{{$item->id}}">
                                                Max quantity is {{ $item->quantity_confirmed - $shipped[$item->product_id] }}
                                            </div>
                                        @endif
                                    @else
                                        <div class="input-group flex-nowrap">
                                            <input id="item-invoice-{{$item->id}}" data-max="{{ $item->quantity_confirmed }}" class="form-control input-sm quantity_shipped" type="text" placeholder="{{ $item->quantity_confirmed }}" value="" size="3" name="quantity_shipped" data-product="{{ $item->product_id }}">
                                        </div>   
                                        <div class="invalid-feedback" id="item-invoice-err-{{$item->id}}">
                                            Max quantity is {{ $item->quantity_confirmed }}
                                        </div>     

                                    @endif
                                    
                                </td>
                                
                                <td style="text-align: center;">
                                    <span id="item-shipped-{{ $item->id }}">
                                        @if (!empty($shipped[$item->product_id]) ) {{ $shipped[$item->product_id] }} @else 0 @endif
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <!-- {{ ($purchase_order->supplier->is_international) ? __('app.currency_symbol_usd') : __('app.currency_symbol_inr')}}{{ number_format($item->buying_price,2) }} -->
                                    <div class="input-group flex-nowrap">
                                        <span class="input-group-text">{{ ($purchase_order->supplier->is_international) ? __('app.currency_symbol_usd') : __('app.currency_symbol_inr')}}</span>
                                        <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->buying_price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->buying_price }}" style="width: 60px;">
                                    </div>
                                </td>
                                <td class="d-none">
                                    {{ number_format($item->tax,2) }}%
                                </td>
                                <td style="text-align: right;">
                                    <span class="item-total" id="row-total-{{ $item->id }}" data-value="{{ $item->total_price }}">
                                        {{ ($purchase_order->supplier->is_international) ? __('app.currency_symbol_usd') : __('app.currency_symbol_inr')}}{{ number_format($item->total_price,2) }}
                                    </span>
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
                <h4 class="header-title mb-3">Order Summary #{{ $purchase_order->order_number }}</h4>

                <div class="table-responsive">
                    <table class="table mb-0" id="table-summary">
                        <tbody>
                            <tr>
                                <td style="text-align: right;" width="30%">Order Total :</td>
                                <td>
                                    <!-- <span>{{ ($purchase_order->supplier->is_international) ? __('app.currency_symbol_usd') : __('app.currency_symbol_inr')}}</span> -->
                                    <span id="grand-total">{{ $purchase_order->amount_usd }}</span>
                                </td>
                            </tr>
                            
                            @if ($purchase_order->supplier->is_international)
                            <tr>
                                <td style="text-align: right;">Exchange Rate:</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="order-echange-rate">{{ $purchase_order->order_exchange_rate }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Amount : </td>
                                <td>
                                    <!-- <span>{{ __('app.currency_symbol_inr')}}</span> -->
                                    <span id="amount-inr">{{ $purchase_order->amount_inr }}</span>
                                </td>
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
        @include('purchase_orders.info_cards')
    </div>
    <div class="col-lg-4">
        <div class="mt-lg-0 rounded @if ($purchase_order->status != 3 && $purchase_order->status != 4) d-none @endif">
            <div class="card ">
                <div class="card-body">
                    <form name="ship-order-form" class="form-invoice needs-validation" novalidate
                        action="{{ route('purchase-order-invoices.store') }}" method="POST">
                        @csrf()
                        <input type="hidden" name="purchase_order_id" id="purchase-order-id" value="{{ $purchase_order->id }}" />
                        <div class="row mb-3">
                            <div class="col-xl-4" id="invoice_number">
                                <label class="form-label">Invoice #</label>
                                <input type="text" class="form-control invoice_number" name="invoice_number" required>
                                <div class="invalid-feedback">
                                    Invoice number is required
                                </div>
                                <div class="invalid-feedback invoice_number_taken">
                                    Invoice number is already taken
                                </div>
                            </div>
                            <div class="col-xl-4" id="shipped_at">
                                <label class="form-label">Shipping date</label>
                                <input type="text" class="form-control" name="shipped_at"
                                data-provide="datepicker" 
                                data-date-container="#shipped_at"
                                data-date-autoclose="true"
                                data-date-format="M d, yyyy"
                                data-date-start-date="-1m"
                                data-date-end-date="+6m"
                                data-date-today-highlight="true"
                                required>
                                <div class="invalid-feedback">
                                    Shipping date is required
                                </div>
                            </div>
    
                        </div>
                    
                        <button class="col-lg-12 text-center btn btn-warning" type="submit"
                            name="ship_order">Add invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">    
    @include('purchase_orders.invoices')
    @include('purchase_orders.log')
</div>
@endsection

@section('page-scripts')
 <script>
       
    $(document).ready(function () {
        "use strict";

        // format number to US dollar
        let USDollar = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
        });

        // format number to Indian rupee
        let INRupee = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        });

        var is_international = $("#is_international").val();
        
        if (is_international=='Y') 
            $("#grand-total").html(USDollar.format($("#grand-total").html()))
        else
            $("#grand-total").html(INRupee.format($("#grand-total").html()))

        $("#amount-inr").html(INRupee.format($("#amount-inr").html()))

        function recalculateGrandTotal(){
            var grand_total = 0;
            var exchange_rate = parseFloat($("#order-echange-rate").html())
            var amount_inr = 0;

            $('.item-total').each(function( index ){
                grand_total = grand_total + parseFloat($(this).attr('data-value'));
            });
            $('#grand-total').html(USDollar.format(grand_total));

            amount_inr = grand_total * exchange_rate;
            $("#amount-inr").html(INRupee.format(amount_inr))

            var route = '{{ route("purchase-orders.update", ":id") }}'; 
            route = route.replace(':id', $('#purchase-order-id').val());
            $.ajax({
                    type: 'POST',
                    url: route,
                    dataType: 'json',
                    data: { 
                        'value' : false , 
                        'field': 'total', 
                        'item': false,
                        '_method': 'PUT'
                    },
                    success : function(result){
                        console.log('total result ' + JSON.stringify(result));
                    }
                });
        }

        $('body').on('blur click', '.editable-field', function(e){

            if ($(this).val() != $(this).attr('data-value')){

                //$('.invalid-feedback').hide();
                //$(this).css('border-color','');

                if ($(this).attr('data-field') == "price"){
                    item_id = $(this).parent().parent().parent().attr('data-id');
                }
                else{
                    var item_id = $(this).parent().parent().parent().attr('data-id');

                    let qty = parseInt($('#item-quantity-' + item_id).val())
                    let shipped = parseInt($('#item-shipped-'+item_id).html())
                    let balance = (qty - shipped)
//                  console.log('qty',qty,'shipped',shipped,'balance',balance)

                    if(balance < 0) {
                        //$(this).css('border-color','#fa5c7c');
                        //$(this).parent().next().show();
                        $.NotificationApp.send("Error","Quantity cannot be less than Quantity Received ","top-center","","error")
                        $(this).val($(this).attr('data-value'));
                        return false
                    }
                    else {
                        $('#item-invoice-' + item_id).attr('placeholder', balance)
                        $('#item-invoice-' + item_id).attr('data-max', balance)
                        $('#item-invoice-err-'+ item_id).html('Max quantity is ' + balance)
                    }
                }
                var total = $('#item-price-' + item_id).val() * $('#item-quantity-' + item_id).val();

                if (is_international=='Y') {
                    $("#row-total-"+ item_id).html(USDollar.format(total));
                }
                else {
                    $("#row-total-"+ item_id).html(INRupee.format(total));
                }
                $("#row-total-"+ item_id).attr('data-value', total);
                
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });

                var route = '{{ route("purchase-orders-items.update", ":id") }}';
                route = route.replace(':id', item_id);
                $.ajax({
                    type: 'POST',
                    url: route,
                    dataType: 'json',
                    data: { 
                        'value' : $(this).val(), 
                        'field': $(this).attr('data-field'), 
                        'item': $(this).attr('data-item'),
                        '_method': 'PUT'
                    },
                    success : function(result){
                        $('#item-price-' + item_id).attr('data-value', $('#item-price-' + item_id).val())
                        $('#item-quantity-' + item_id).attr('data-value', $('#item-quantity-' + item_id).val())
                        $('#quantity-shipped-'+item_id).val()
                        recalculateGrandTotal()
                    }
                });
            }
        });

        $('.form-invoice').on('submit', function(e){
            e.preventDefault();   
         
            $('.invalid-feedback').hide();
            $('.quantity_shipped').css('border-color','');

            var qty_error = false;
            $('.product_shipped').remove();
            $( ".quantity_shipped" ).each(function( index ) {
                if ($(this).val() > 0){
                    if (parseInt($(this).val()) <= parseInt($(this).attr('data-max'))){
                        var field = "<input type=\"hidden\" class=\"product_shipped\" name=\"products[" + $( this ).attr('data-product') + "]\" value=\"" + $(this).val() + "\" />";
                        $(field).appendTo('.form-invoice');
                    }
                    else{
                        qty_error = true;
                        $(this).css('border-color','#fa5c7c');
                        $(this).parent().next().show();
                    
                    }
                }
            });

            if ($(".product_shipped").length > 0 && qty_error == false){
                $.ajax({
                    type: 'POST',
                    url: $(this).attr("action"),
                    dataType: 'json',
                    data: $( this ).serialize(),
                    success: function (data) {
                        window.location.replace(data['redirect']);
                    },
                    error:function(xhr, textStatus, thrownError, data){
                        $('.invoice_number_taken').show();
                        $('.form-invoice').removeClass('was-validated');
                        console.log("Error: " + thrownError);
                        console.log("Error: " + textStatus);
                    }
                }); 
            }
            else{
                $( ".quantity_shipped" ).each(function( index ) {
                    $(this).css('border-color','#fa5c7c');
                });
            }
        });

    }); 
</script>

@endsection
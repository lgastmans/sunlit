@extends('layouts.app')

@section('title')
    @parent() | PO #{{ $purchase_order->order_number }}
@endsection

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
                                <th>Quantity Confirmed</th>
                                <th>Quantity Remaining</th>
                                <th>Quantity Received</th>
                                <th>Price</th>
                                <th class="d-none">Tax</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase_order->items as $item)
                            <tr>
                                <td>{{ $item->product->part_number }}</td>
                                <td>{{ $item->quantity_confirmed }}</td>
                                <td>
                                    @if (!empty($shipped[$item->product_id]))
                                        @if ($item->quantity_confirmed <= $shipped[$item->product_id])
                                            <span class="badge bg-success">Nothing to receive</span>
                                        @else 
                                            <div class="input-group flex-nowrap">
                                                <input class="form-control input-sm quantity_shipped" type="text" value="{{ $item->quantity_confirmed -  $shipped[$item->product_id] }}" size="3" name="quantity_shipped" data-product="{{ $item->product_id }}">
                                            </div>
                                        @endif
                                    @else
                                        <div class="input-group flex-nowrap">
                                            <input class="form-control input-sm quantity_shipped" type="text" value="{{ $item->quantity_confirmed }}" size="3" name="quantity_shipped" data-product="{{ $item->product_id }}">
                                        </div>        
                                    @endif
                                </td>
                                <td>@if (!empty($shipped[$item->product_id]) ) {{ $shipped[$item->product_id] }} @else 0 @endif</td>
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
                <h4 class="header-title mb-3">Order Summary #{{ $purchase_order->order_number }}</h4>

                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Order Total :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_usd')}}</span>
                                    <span id="grand-total">{{ $purchase_order->amount_usd }}</span>
                                </td>
                            </tr>
                          
                            <tr>
                                <td>Exchange Rate:</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="order-echange-rate">{{ $purchase_order->order_exchange_rate }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Amount : </td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="amount-inr">{{ $purchase_order->amount_inr }}</span>
                                </td>
                            </tr>
                            
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
                        <input type="hidden" name="purchase_order_id" value="{{ $purchase_order->id }}" />
                        <div class="row mb-3">
                            <div class="col-xl-4" id="invoice_number">
                                <label class="form-label">Invoice #</label>
                                <input type="text" class="form-control" name="invoice_number" required>
                                <div class="invalid-feedback">
                                    Invoice number is required
                                </div>
                            </div>
                            <div class="col-xl-4" id="shipped_at">
                                <label class="form-label">Shipping date</label>
                                <input type="text" class="form-control" name="shipped_at"
                                data-provide="datepicker" 
                                data-date-container="#shipped_at"
                                data-date-autoclose="true"
                                data-date-format="M d, yyyy"
                                required>
                                <div class="invalid-feedback">
                                    Shipping date is required
                                </div>
                            </div>
                            <div class="col-xl-4" id="due_at">
                                <label class="form-label">Due date</label>
                                <input type="text" class="form-control" name="due_at"
                                data-provide="datepicker" 
                                data-date-container="#due_at"
                                data-date-autoclose="true"
                                data-date-format="M d, yyyy"
                                required>
                                <div class="invalid-feedback">
                                    Due date is required
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3" >
                            <div class="col-xl-6" id="courier">
                                <label class="form-label">Courier</label>
                                <input type="text" class="form-control" name="courier" required>
                                <div class="invalid-feedback">
                                    Courier is required
                                </div>
                            </div>
                            <div class="col-xl-6" id="tracking_number">
                                <label class="form-label">Tracking number</label>
                                <input type="text" class="form-control" name="tracking_number" required>
                                <div class="invalid-feedback">
                                   Tracking number is required
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
        
        // add product_id and quantity shipped to form before submitting
        $('.form-invoice').on('submit', function(e){
            e.preventDefault();    

            $(this).css('border-color','');

            $('.product_shipped').remove();
            $( ".quantity_shipped" ).each(function( index ) {
                if ($(this).val() > 0){
                    var field = "<input type=\"hidden\" class=\"product_shipped\" name=\"products[" + $( this ).attr('data-product') + "]\" value=\"" + $(this).val() + "\" />";
                    $(field).appendTo('.form-invoice');
                }
            });
            if ($(".product_shipped").length > 0){
                $.ajax({
                    type: 'POST',
                    url: $(this).attr("action"),
                    dataType: 'json',
                    data: $( this ).serialize(),
                    success: function (data) {
                        window.location.replace(data['redirect']);
                    },
                    error:function(xhr, textStatus, thrownError, data){
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
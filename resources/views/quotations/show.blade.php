@extends('layouts.app')

@section('title')
    @parent() | Q #{{ $order->quotation_number }}
@endsection

@section('page-title')
    Q #{{ $order->quotation_number}}
@endsection

@section('content')

@include('quotations.steps')

@if ($order->status <= 2)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('quotation-items.store') }}" method="POST" class="form-product row">
                        <input type="hidden" name="quotation_id" id="quotation-id" value="{{ $order->id }}">
                        <input type="hidden" name="quotation_number_slug" id="quotation_number_slug" value="{{ $order->quotation_number_slug }}">
                        <input type="hidden" name="warehouse_id" id="warehouse-id" value="{{ $order->warehouse->id }}">
                        
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label class="form-label" for="product-select">Product</label>
                                <select class="product-select form-control" name="product_id" id="product_id"></select>
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="mb-3">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input type="text" class="form-control" name="quantity" id="quantity"  value="1">
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="mb-3">
                                <label class="form-label" for="price">Price</label>
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                    <input type="selling_price" class="form-control" name="price"  id="price" value="" aria-describedby="sellingPriceHelp">
                                    {{-- <span class="input-group-text" display="none" id="suggested_selling_price"></span> --}}
                                    <input type="hidden" name="tax" id="tax">
                                </div>
                                <div id="sellingPriceHelp" class="form-text"><span display="none" id="suggested_selling_price"></span></div>                            
                            </div>
                        </div>
                        <div class="col-lg-1">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button class="add-product btn btn-primary form-control" type="submit">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Items from Q #{{ $order->quotation_number }}</h4>

                <x-forms.errors class="mb-4" :errors="$errors" />

                <div class="table-responsive">
                    <table class="table mb-0" id="quotation-items-table">
                        <thead class="table-light">
                            <tr>
                                <th class="col-5">Product</th>
                                <th class="col-2">Quantity</th>
                                <th class="col-1">Price</th>
                                <th class="col-1">Tax</th>
                                <th class="col-2">Total</th>
                                <th class="col-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @if ($order->status == 4)
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
                                            <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity }}" class="editable-field form-control" data-value="{{ $item->quantity }}" data-field="quantity" data-item="{{ $item->id }}"
                                                placeholder="Qty" style="width: 120px;">
                                        </td>
                                        <td>
                                            <div class="input-group flex-nowrap">
                                                <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                                <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->price }}" style="width: 150px;">
                                            </div>
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
                <input type="hidden" name="quotation_id" id="quotation-id" value="{{ $order->id }}">
                <input type="hidden" name="dealer_id" id="dealer-id" value="{{ $order->dealer->id }}">

                <h4 class="header-title mb-3">Quotation Summary #{{ $order->quotation_number }}</h4>

                <a class="btn btn-success" href="{{ route('quotations.proforma-pdf', $order->quotation_number_slug) }}" role="button" target="_blank">Export to PDF</a>

                <a class="btn btn-success" href="{{ route('quotations.proforma', $order->quotation_number_slug) }}" role="button" target="_blank">View Quotation</a>


                <div class="table-responsive">
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td>Total :</td>
                                <td>
                                    <span>{{ __('app.currency_symbol_inr')}}</span>
                                    <span id="grand-total">{{ $grand_total }}</span>
                                </td>
                            </tr>
                          
                         
{{--                             @if ($order->status >= 2 && $order->status <= 3)
 --}}
                                <tr>
                                    <td>Transport Charges: </td>
                                    <td>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="transport-charges">{{ $order->transport_total ?? '0.00' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Grand Total :</th>
                                    <th>
                                        <span>{{ __('app.currency_symbol_inr')}}</span>
                                        <span id="total-cost">{{ $order->total }}</span>
                                    </th>
                                </tr>
                            {{-- @endif --}}

                        </tbody>
                    </table>
                    
                </div>
                <!-- end table-responsive -->

            </div> {{-- card body --}}

            @if ($order->status <= 2)
                <div class="card-footer">
                    <div class="row mb-3" >
                        <div class="col-xl-6" id="transport_charges">
                            <label class="form-label">Transport Charges</label>
                            <div class="input-group">
                                <span class="input-group-text" id="cleared__currency">{{ __('app.currency_symbol_inr')}}</span>
                                <input type="text" class="form-control" name="transport_charges" id="freight" value="{{ $order->transport_charges}}" required>
                                <div class="invalid-feedback">
                                    Transport Charges is required
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="input-group">
                            </div>
                        </div>
                    </div>
                </div> {{-- card footer --}}
            @endif

        </div> <!-- card -->
    </div> <!-- end col -->
</div>



<div class="row">
    <div class="col-lg-8">     
    </div>
    <div class="col-lg-4">

        @if ($order->status == 1)
        <div class="place-order-form-container mt-4 mt-lg-0 rounded @if (count($order->items)==0) d-none @endif">
            <div class="card mt-4 border">
                <div class="card-body">
                    <form name="place-order-form" action="{{ route('quotations.pending', $order->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @method('PUT')
                        <div class="mb-3 position-relative" id="pending_at">
                            <label class="form-label">Pending date</label>
                            <input type="text" class="form-control" name="pending_at" value="{{ $order->display_pending_at }}"
                            data-provide="datepicker" 
                            data-date-container="#pending_at"
                            data-date-autoclose="true"
                            data-date-format="M d, yyyy"
                            data-date-start-date="-1m"
                            data-date-end-date="+6m"
                            data-date-today-highlight="true"
                            required>
                            <div class="invalid-feedback">
                                Pending date is required
                            </div>
                        </div>
                        
                        <button class="col-lg-12 text-center btn btn-warning" type="submit" name="place_order"><i class="mdi mdi-cart-plus me-1"></i>Pending</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if ($order->status == 2)
        <div class="place-order-form-container mt-4 mt-lg-0 rounded @if (count($order->items)==0) d-none @endif">
            <div class="card mt-4 border">
                <div class="card-body">
<!--                     <form name="place-order-form" action="{{ route('quotations.confirmed', $order->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @method('PUT') -->
                        <div class="mb-3 position-relative" id="confirmed_at">
                            <label class="form-label">Confirmed date</label>
                            <input type="text" class="form-control" name="confirmed_at" value="{{ $order->display_confirmed_at }}"
                            data-provide="datepicker" 
                            data-date-container="#confirmed_at"
                            data-date-autoclose="true"
                            data-date-format="M d, yyyy"
                            data-date-start-date="-1m"
                            data-date-end-date="+6m"
                            data-date-today-highlight="true"
                            required>
                            <div class="invalid-feedback">
                                Confirmed date is required
                            </div>
                        </div>
                        
                        <button id="{{ $order->id }}" class="col-lg-12 text-center btn btn-warning" type="submit" name="place_order" data-bs-toggle="modal" data-bs-target="#create-invoice-modal-order"><i class="mdi mdi-cart-plus me-1"></i>Confirmed</button>
                    <!-- </form> -->
                </div>
            </div>
        </div>
        @endif

        @if ($order->status <= 2)            
            <div class="mt-4 mt-lg-0 rounded">
                <div class="card mt-4 border">
                    <div class="card-body">
                        <button id="{{ $order->id }}" class="col-lg-12 text-center btn btn-danger" type="submit" name="delete_order" data-bs-toggle="modal" data-bs-target="#delete-modal-order"><i class="mdi mdi-delete"></i> Delete order</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>


<div class="row">    
    @include('quotations.log')
</div>


<!-- 
    modal confirm create PI 
-->
<div id="create-invoice-modal-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="create-modalLabelOrder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="create-invoice-order-form" action="" method="POST">
                @method("PUT")
                @csrf()
                <div class="modal-header modal-colored-header bg-danger">
                    <h4 class="modal-title" id="create-modalLabelOrder">Create Invoice</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    {{ __('app.create_confirm', ['field' => "Invoice" ]) }} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('app.modal_close') }}</button>
                    <button type="button" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('create-invoice-order-form').submit();">{{ __('app.modal_create') }}</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!--
    modal delete quotation 
-->
<div id="delete-modal-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete-modalLabelOrder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-order-form" action="" method="POST">
                @method("DELETE")
                @csrf()
                <div class="modal-header modal-colored-header bg-danger">
                    <h4 class="modal-title" id="delete-modalLabelOrder">Delete Quotation {{ $order->quotation_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    {{ __('app.delete_confirm', ['field' => "Quotation" ]) }}
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
            quotation_update : '{{ route("quotations.update", ":id") }}',
            //ajax_dealers : '{{route("ajax.dealers")}}',
            product_json : '{{ route("product.json", [":id", ":warehouse_id"]) }}',
            quotation_items_update : '{{ route("quotation-items.update", ":id") }}',
            quotation_items_delete : '{{ route("quotation-items.delete", ":id") }}',
            quotation_delete : '{{ route("quotations.delete", ":id") }}',
            product_route : '{{ route("ajax.products.warehouse", [":warehouse_id"]) }}',
            inr_symbol : '{{ __("app.currency_symbol_inr")}}',
            quotation_create_invoice: '{{ route("quotations.confirmed", ":id") }}'
        };

        $(document).ready(function () {
            "use strict";

            $("#freight").blur(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                }); 

                var route = '{{ route("quotations.update", ":id") }}';
                route = route.replace(':id', $('#quotation-id').val());

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
            }); // freight / transport charges            


        }); //document ready

    </script>

    <script src="{{ asset('js/pages/quotations.js') }}"></script>

@endsection
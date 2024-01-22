@extends('layouts.app')

@section('title')
    @parent() | Credit Note #{{ $order->credit_note_number }}
@endsection

@section('page-title')
    Credit Note #{{ $order->credit_note_number}}
@endsection

@section('content')

@include('credit_notes.steps')

<style>
    #table-summary tr > td {
      padding-top:3px !important;
      padding-bottom:3px !important;
      padding-right:20px;   
    }    
</style>

@if ($order->status <= 1)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('credit-note-items.store') }}" method="POST" class="form-product row">
                        <input type="hidden" name="credit_note_id" id="credit-note-id" value="{{ $order->id }}">
                        <input type="hidden" name="credit_note_number_slug" id="credit_note_number_slug" value="{{ $order->credit_note_number_slug }}">
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
                <h4 class="header-title mb-3">Items from C.N. #{{ $order->credit_note_number }}</h4>

                <x-forms.errors class="mb-4" :errors="$errors" />

                <div class="table-responsive">
                    <table class="table mb-0" id="credit-note-items-table">
                        <thead class="table-light">
                            <tr>
                                <th class="col-4">Product</th>
                                <th class="col-2" style="text-align: center;">Quantity</th>
                                <th class="col-2" style="text-align: center;">Price</th>
                                <th class="col-1" style="text-align: center;">Tax</th>
                                <th class="col-2" style="text-align: right;">Total</th>
                                <th class="col-1" style="text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                @if ($order->status == 2)
                                     <tr>
                                        <td>{{ $item->product->part_number }}</td>
                                        <td style="text-align: right;">{{ $item->quantity }}</td>
                                        <td style="text-align: right;">{{ __('app.currency_symbol_inr')}}{{ number_format($item->price,2) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->tax,2) }}%</td>
                                        <td style="text-align: right;">{{ __('app.currency_symbol_inr')}}{{ $item->total_price }}</td>
                                        <td></td>
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
                                                <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity }}" class="editable-field form-control" data-value="{{ $item->quantity }}" data-field="quantity" data-item="{{ $item->id }}" placeholder="Qty" style="width: 120px;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group flex-nowrap">
                                                <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                                <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->price }}" style="width: 150px;">
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            <span id="item-tax-{{ $item->id }}">@if ($item->tax){{ $item->tax }}@else 0.00 @endif%</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span id="item-total-{{ $item->id }}" class="item-total"  data-value="{{ $item->total_price_unfmt }}">
                                                {{__('app.currency_symbol_inr')}}{{ $item->total_price }}
                                            </span>
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
                <input type="hidden" name="credit_note_id" id="credit-note-id" value="{{ $order->id }}">
                <input type="hidden" name="dealer_id" id="dealer-id" value="{{ $order->dealer->id }}">

                <h4 class="header-title mb-3">Credit Note Summary #{{ $order->credit_note_number }}</h4>

                <a class="btn btn-success mb-2" href="{{ route('credit-notes.proforma-pdf', $order->credit_note_number_slug) }}" role="button" target="_blank">Export to PDF</a>

                <a class="btn btn-success mb-2" href="{{ route('credit-notes.proforma', $order->credit_note_number_slug) }}" role="button" target="_blank">View Credit Note</a>

                <div class="table-responsive">
                    <table class="table mb-0" id="table-summary">
                        <tbody>
                            <tr>
                                <td style="text-align: right;" width="30%">Against Invoice :</td>
                                <td>{{ $order->is_against_invoice == 1 ? "Yes" : "No" }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Invoice Number :</td>
                                <td>{{ $order->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Invoice Date :</td>
                                <td>{{ $order->display_invoice_date }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: right;">Total :</td>
                                <td>
                                    <span></span>
                                    <span id="grand-total">{{ $grand_total }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
                <br>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Dealer Information</h4>
                        <h5>{{ $order->dealer->company }}</h5>
                        <h6>{{ $order->dealer->contact_person }}</h6>
                        <address class="mb-0 font-14 address-lg">
                            <abbr title="Phone">P:</abbr> {{ $order->dealer->phone }} <br />
                            <abbr title="Mobile">M:</abbr> {{ $order->dealer->phone2 }} <br />
                            <abbr title="Mobile">@:</abbr> {{ $order->dealer->email }}
                        </address>
                    </div>
                </div>

            </div> {{-- card body --}}

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
                    <form name="place-order-form" action="{{ route('credit-notes.confirmed', $order->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @method('PUT')
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
                        
                        <button class="col-lg-12 text-center btn btn-warning" type="submit"><i class="mdi mdi-cart-plus me-1"></i>Confirmedd</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        @if ($order->status <= 1)            
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
    @include('credit_notes.log')
</div>


<!--
    modal delete credit note 
-->
<div id="delete-modal-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete-modalLabelOrder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-order-form" action="" method="POST">
                @method("DELETE")
                @csrf()
                <div class="modal-header modal-colored-header bg-danger">
                    <h4 class="modal-title" id="delete-modalLabelOrder">Delete Credit Note {{ $order->credit_note_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    {{ __('app.delete_confirm', ['field' => "Credit Note" ]) }}
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
            credit_note_update : '{{ route("credit-notes.update", ":id") }}',
            //ajax_dealers : '{{route("ajax.dealers")}}',
            product_json : '{{ route("product.json", [":id", ":warehouse_id"]) }}',
            credit_note_items_update : '{{ route("credit-note-items.update", ":id") }}',
            credit_note_items_delete : '{{ route("credit-note-items.delete", ":id") }}',
            credit_note_delete : '{{ route("credit-notes.delete", ":id") }}',
            product_route : '{{ route("ajax.products.warehouse", [":warehouse_id"]) }}',
            inr_symbol : '{{ __("app.currency_symbol_inr")}}',
            //credit_note_confirm: '{{ route("credit-notes.confirmed", ":id") }}'
        };



        $(document).ready(function () {
            "use strict";

        }); //document ready

    </script>

    <script src="{{ asset('js/pages/credit_notes.js') }}"></script>

@endsection
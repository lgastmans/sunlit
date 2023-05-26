@extends('layouts.app')

@section('title')
    @parent() | PI #{{ $order->order_number }}
@endsection

@section('page-title')
    Proforma Invoice <span class="order-number">#{{ $order->order_number }}</span>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('sale-orders-items.store') }}" method="POST" class="form-product row">
                    <input type="hidden" name="sale_order_id" id="sale-order-id" value="{{ $order->id }}">
                    <input type="hidden" name="order_number_slug" id="order_number_slug" value="{{ $order->order_number_slug }}">
                    <input type="hidden" name="dealer_id" id="dealer-id" value="{{ $order->dealer->id }}">
                    <input type="hidden" name="warehouse_id" id="warehouse-id" value="{{ $order->warehouse  ->id }}">
                    
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Product</label>
                            <select class="product-select form-control" name="product_id" id="product_id"></select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="mb-3">
                            <label class="form-label" for="quantity_ordered">Quantity</label>
                            <input type="text" class="form-control" name="quantity_ordered" id="quantity_ordered"  value="1">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="mb-3">
                            <label class="form-label" for="selling_price">Price</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                <input type="selling_price" class="form-control" name="selling_price"  id="selling_price" value="" aria-describedby="sellingPriceHelp">
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="table-responsive">
                            <table class="table table-borderless table-centered mb-0" id="sale-order-items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-5">Product</th>
                                        <th class="col-1">Quantity</th>
                                        <th class="col-2">Price</th>
                                        <th class="col-1">Tax</th>
                                        <th class="col-2">Total</th>
                                        <th class="col-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->items as $item) 
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
                                                <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity_ordered }}" class="editable-field form-control" data-value="{{ $item->quantity_ordered }}" data-field="quantity" data-item="{{ $item->id }}"
                                                    placeholder="Qty" style="width: 120px;">
                                            </td>
                                            <td>
                                                <div class="input-group flex-nowrap">
                                                    <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                                    <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->selling_price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->selling_price }}">
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
                                    @empty
                                        <tr class="no-items"><td>You haven't added any product to the order, yet!</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                        </div> <!-- end table-responsive-->

                    </div>
                    <!-- end col -->

                    <div class="col-lg-3">
                        <div class="border p-3 mt-4 mt-lg-0 rounded">
                            <h4 class="header-title mb-3">Order Summary <span class="order-number">#{{ $order->order_number }}</span><i class="mdi mdi-square-edit-outline ms-2 edit-order-number"></i>
                                <form class="row mt-1 edit-order-number-form" style="display:none" method="POST" action="{{ route('sale-orders.update', $order->id) }}">
                                    @csrf()
                                    @method('PUT')
                                    <input type="hidden" name="field" value="order_number">
                                    <div class="col-xl-5">
                                        <input type="text" class="form-control col-xl-1" id="order_number" name="order_number" placeholder="" value="{{ $order->order_number }}">
                                        
                                    </div>
                                    <div class="col-xl-2">
                                        <button class="btn btn-secondary btn-sm" type="submit">Update</button>
                                    </div>
                                    <div class="col-xl-2">
                                        <button class="btn btn-danger btn-sm edit-order-number-cancel" type="button">Cancel</button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Order number already exist
                                    </div>
                                </form>
                                
                            </h4>

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Order Total :</td>
                                            <td>
                                                <span>{{ __('app.currency_symbol_inr')}}</span>
                                                <span id="grand-total">{{ $order->amount }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                            <!-- end table-responsive -->
                        </div>
                        <div class="place-order-form-container mt-4 mt-lg-0 rounded @if (count($order->items)==0) d-none @endif">
                            <div class="card mt-4 border">
                                <div class="card-body">
                                    <form name="place-order-form" action="{{ route('sale-orders.blocked', $order->id) }}" method="POST" class="needs-validation" novalidate>
                                        @csrf()
                                        @method('PUT')
                                        <div class="mb-3 position-relative" id="blocked_at">
                                            <label class="form-label">Blocked date</label>
                                            <input type="text" class="form-control" name="blocked_at" value="{{ $order->display_blocked_at }}"
                                            data-provide="datepicker" 
                                            data-date-container="#blocked_at"
                                            data-date-autoclose="true"
                                            data-date-format="M d, yyyy"
                                            data-date-start-date="-1m"
                                            data-date-end-date="+6m"
                                            data-date-today-highlight="true"
                                            required>
                                            <div class="invalid-feedback">
                                                Blocked date is required
                                            </div>
                                        </div>
                                        
                                        <button class="col-lg-12 text-center btn btn-warning" type="submit" name="place_order"><i class="mdi mdi-cart-plus me-1"></i>Block order</button>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <div class="mt-4 mt-lg-0 rounded">
                            <div class="card mt-4 border">
                                <div class="card-body">
                                    <button id="{{ $order->id }}" class="col-lg-12 text-center btn btn-danger" type="submit" name="delete_order" data-bs-toggle="modal" data-bs-target="#delete-modal-order"><i class="mdi mdi-delete"></i>Delete order</button>
                                </div>
                            </div>
                        </div>
    
                    </div> <!-- end col -->
                    

                </div> <!-- end row -->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
<!-- end row -->
<div class="row">
    <div class="col-lg-4">
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
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Warehouse Information</h4>
                <h5>{{ $order->warehouse->name }}</h5>
                <h6>{{ $order->warehouse->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $order->warehouse->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $order->warehouse->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $order->warehouse->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->
    
</div>
<!-- end row -->

<div id="delete-modal-order" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete-modalLabelOrder" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-order-form" action="" method="POST">
                @method("DELETE")
                @csrf()
                <div class="modal-header modal-colored-header bg-danger">
                    <h4 class="modal-title" id="delete-modalLabelOrder">Delete Proforma Invoice {{ $order->order_number }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    {{ __('app.delete_confirm', ['field' => "Sale Order" ]) }}
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
                sale_order_update : '{{ route("sale-orders.update", ":id") }}',
                ajax_dealers : '{{route("ajax.dealers")}}',
                product_json : '{{ route("product.json", [":id", ":warehouse_id"]) }}',
                sale_order_items_update : '{{ route("sale-orders-items.update", ":id") }}',
                sale_order_items_delete : '{{ route("sale-orders-items.delete", ":id") }}',
                sale_order_delete : '{{ route("sale-orders.delete", ":id") }}',
                product_route : '{{ route("ajax.products.warehouse", [":warehouse_id"]) }}',
                inr_symbol : '{{ __("app.currency_symbol_inr")}}'
            };

    </script>

    <script src="{{ asset('js/pages/sale_order_cart.js') }}"></script>

@endsection
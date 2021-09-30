@extends('layouts.app')

@section('title')
    @parent() | Sale #{{ $order->order_number }}
@endsection

@section('page-title')
    Sale Order <span class="order-number">#{{ $order->order_number }}</span>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('sale-orders-items.store') }}" method="POST" class="form-product row">
                    <input type="hidden" name="sale_order_id" id="sale-order-id" value="{{ $order->id }}">
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
                                <input type="selling_price" class="form-control" name="selling_price"  id="selling_price" value="">
                                <input type="hidden" name="tax" id="tax">
                            </div>
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
                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table class="table table-borderless table-centered mb-0" id="sale-order-items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-5">Product</th>
                                        <th class="col-2">Price</th>
                                        <th class="col-1">Quantity</th>
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
                                                        class="text-body product-name">{{ $item->product->name }}</a>
                                                    <br>
                                                    <small class="me-2"><b>Code:</b> <span class="product-code">{{  $item->product->code }}</span> </small>
                                                    <small><b>Model:</b> <span class="product-model">{{ $item->product->model }}</span>
                                                    </small>
                                                </p>
                                            </td>
                                            <td>
                                                <div class="input-group flex-nowrap">
                                                    <span class="input-group-text">{{ __('app.currency_symbol_inr')}}</span>
                                                    <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->selling_price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->selling_price }}">
                                                </div>
                                            </td>
                                            <td>
                                                <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity_ordered }}" class="editable-field form-control" data-value="{{ $item->quantity_ordered }}" data-field="quantity" data-item="{{ $item->id }}"
                                                    placeholder="Qty" style="width: 90px;">
                                            </td>
                                            <td>
                                                <span id="item-tax-{{ $item->id }}">@if ($item->tax){{ $item->tax }}@else 0.00 @endif%</span>
                                            </td>
                                            <td>
                                                <span>{{ __('app.currency_symbol_usd')}}</span>
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

                    <div class="col-lg-4">
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
                                        <button class="btn btn-secondary" type="submit">Update</button>
                                    </div>
                                    <div class="col-xl-2">
                                        <button class="btn btn-danger edit-order-number-cancel" type="button">Cancel</button>
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
                                    <form name="place-order-form" action="{{ route('sale-orders.ordered', $order->id) }}" method="POST" class="needs-validation" novalidate>
                                        @csrf()
                                        @method('PUT')
                                        <div class="mb-3 position-relative" id="ordered_at">
                                            <label class="form-label">Ordered date</label>
                                            <input type="text" class="form-control" name="ordered_at" value="{{ $order->display_ordered_at }}"
                                            data-provide="datepicker" 
                                            data-date-container="#ordered_at"
                                            data-date-autoclose="true"
                                            data-date-format="M d, yyyy"
                                            required>
                                            <div class="invalid-feedback">
                                                Ordered date is required
                                            </div>
                                        </div>
                                        
                                        <button class="col-lg-12 text-center btn btn-warning" type="submit" name="place_order"><i class="mdi mdi-cart-plus me-1"></i> Place order</button>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <div class="mt-4 mt-lg-0 rounded">
                            <div class="card mt-4 border">
                                <div class="card-body">
                                    <button id="{{ $order->id }}" class="col-lg-12 text-center btn btn-danger" type="submit" name="delete_order" data-bs-toggle="modal" data-bs-target="#delete-modal-order"><i class="mdi mdi-delete"></i> Delete order</button>
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
                    <h4 class="modal-title" id="delete-modalLabelOrder">Delete Sale Order {{ $order->order_number }}</h4>
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

    function recalculateGrandTotal(){
        var grand_total = 0;
        $('.item-total').each(function( index ){
            grand_total = grand_total + parseFloat($(this).html());
        });
        $('#grand-total').html(grand_total.toFixed(2));

        var route = '{{ route("sale-orders.update", ":id") }}';
        route = route.replace(':id', $('#sale-order-id').val());
        $.ajax({
                type: 'POST',
                url: route,
                dataType: 'json',
                data: { 
                    'value' : false , 
                    'field': 'amount', 
                    'item': false,
                    '_method': 'PUT'
                },
                success : function(result){
                    //
                }
            });
    }

 
    var dealerSelect = $(".dealer-select").select2();
    dealerSelect.select2({
        ajax: {
            url: '{{route('ajax.dealers')}}',
            dataType: 'json'
        }
    });

    var productSelect = $(".product-select").select2();
    var product_route = '{{ route("ajax.products", ["warehouse",":warehouse_id"]) }}';
    product_route = product_route.replace(':warehouse_id', $('#warehouse-id').val());
    productSelect.select2({
        ajax: {
            url: product_route,
            dataType: 'json'
        }
    });

    productSelect.on("change", function (e) { 
        var product_id = $(".product-select").find(':selected').val();
        var warehouse_id = $(".product-select").find(':selected').val();
        var route = '{{ route("product.json", [":id", ":warehouse_id"]) }}';
        route = route.replace(':id', product_id);
        route = route.replace(':warehouse_id', $('#warehouse-id').val());
        $.ajax({
            type: 'GET',
            url: route,
            dataType: 'json',
            success : function(data){
                $('#selling_price').val(data.average_cost);
                $('#tax').val(data.tax.amount);
            }
        })
    });

    function getTaxValue(tax_percentage){
        if (tax_percentage == null)
            return 1;
        return tax = 1 + (parseFloat(tax_percentage.replace('%','') / 100));
    }

    $('body').on('blur', '.editable-field', function(e){        
        if ($(this).val() != $(this).attr('data-value')){
            if ($(this).attr('data-field') == "price"){
                item_id = $(this).parent().parent().parent().attr('data-id');
            }
            else{
                var item_id = $(this).parent().parent().attr('data-id');
            }
            var total = $('#item-price-' + item_id).val() * getTaxValue($('#item-tax-' + item_id).html()) * $('#item-quantity-' + item_id).val();
            
            $('#item-total-' + item_id).html(total.toFixed(2));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            var route = '{{ route("sale-orders-items.update", ":id") }}';
            route = route.replace(':id', item_id);
            $.ajax({
                type: 'POST',
                url: route,
                dataType: 'json',
                data: { 
                    'value' : $(this).val() , 
                    'field': $(this).attr('data-field'), 
                    'item': $(this).attr('data-item'),
                    '_method': 'PUT'
                },
                success : function(result){
                    $('#item-price-' + item_id).attr('data-value', $('#item-price-' + item_id).val())
                    $('#item-quantity-' + item_id).attr('data-value', $('#item-quantity-' + item_id).val())
                    recalculateGrandTotal()
                }
            });
        }
    });


    $('#delete-modal').on('show.bs.modal', function (e) {
        var route = '{{ route("sale-orders-items.delete", ":id") }}';
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-form').attr('action', route);
        }
    });

    $('#delete-modal-order').on('show.bs.modal', function (e) {
        var route = '{{ route("sale-orders.delete", ":id") }}';
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-order-form').attr('action', route);
        }
    });

    function is_existing_product(product_id)
    {
        item_id = 0;
        $('.item').each(function(index){
            if ($(this).attr('data-product-id') == product_id){
                item_id =  $(this).attr('data-id');
            } 
        }); 
        return item_id;
    }
    

    $('.form-product').on('submit', function(e){
        e.preventDefault();     
        var existing_item_id = is_existing_product($('#product_id').val());
        if (existing_item_id > 0){
            var asked_quantity = $('#quantity_ordered').val();
            var new_quantity = parseInt(asked_quantity) + parseInt($('#item-quantity-'+existing_item_id).val())
            $('#quantity_ordered').val(new_quantity);
        }          
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });   
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            dataType: 'json',
            data: $( this ).serialize(),
            success: function (data) {
                if (existing_item_id > 0){
                    $('.item').each(function(index){
                        if ($(this).attr('data-product-id') == data.product.id){
                            var existing_item_id = $(this).attr('data-id');
                            var existing_item_quantity = $('#item-quantity-'+existing_item_id).val();
                            $('#item-quantity-'+existing_item_id).val(parseInt(data.item.quantity_ordered));
                            var new_total = (data.item.selling_price * getTaxValue(data.item.tax)) * parseInt($('#item-quantity-'+existing_item_id).val()).toFixed(2);
                            $('#item-total-'+existing_item_id).html(new_total.toFixed(2))
                            new_product = false;
                        } 
                    });
                }
                else{
                    var item = '<tr class="item" data-id="'+ data.item.id +'" data-product-id="'+data.product.id+'">';
                    item += '<td>';
                        item += '<p class="m-0 d-inline-block align-middle font-16">';
                            item += '<a href="javascript:void(0); class="text-body product-name">'+ data.product.name +'</a>';
                                item += '<br>';
                                item += '<small class="me-2"><b>Code:</b> <span class="product-code">'+ data.product.code +'</span> </small>';
                                item += '<small><b>Model:</b> <span class="product-model">'+ data.product.model +'</span></small>';
                                item += '</p>';
                                item += '</td>';
                                item += '<td>';
                                    item += '<div class="input-group flex-nowrap">';
                                        item += '<span class="input-group-text">$</span>';
                                        item += '<input id="item-price-'+ data.item.id +'" type="text" class="editable-field form-control" data-value="'+ data.item.selling_price +'" data-field="price" data-item="'+ data.item.id +'" placeholder="" value="'+ data.item.selling_price  +'">';
                                        item += '</div>';
                                        item += '</td>';
                                        item += '<td>';
                                            item += '<input id="item-quantity-'+ data.item.id +'" type="number" min="1" value="'+ data.item.quantity_ordered +'" class="editable-field form-control" data-value="'+ data.item.quantity_ordered +'" data-field="quantity" data-item="'+ data.item.id +'" placeholder="Qty" style="width: 90px;">';
                            item += '</td>';
                            item += '<td>';
                            item += '<span id="item-tax-'+ data.item.id +'" class="item-tax">'+ data.item.tax +'%</span>';
                            item += '</td>';
                            item += '<td>';
                            item += '<span>{{ __('app.currency_symbol_inr')}}</span><span id="item-total-'+ data.item.id +'" class="item-total">'+((data.item.selling_price * getTaxValue(data.item.tax)) * parseInt(data.item.quantity_ordered)).toFixed(2) +'</span>';
                            item += '</td>';
                            item += '<td>';
                            item += '<a href="javascript:void(0);" class="action-icon" id="1" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>';
                            item += '</td>';
                            item += '</tr> ';
                    $('#sale-order-items-table > tbody:last-child').append(item);
                    $('.no-items').remove();
                }
                $('.place-order-form-container').removeClass('d-none');
                recalculateGrandTotal()

            },
            error:function(xhr, textStatus, thrownError, data)
            {
                console.log("Error: " + thrownError);
                console.log("Error: " + textStatus);


            }
        });     
    });


    $('.edit-order-number').on('click', function(e){
        $(this).hide();
        $('.edit-order-number-form').slideDown();
        $('#purchase-order-number').hide();
    });

    $('.edit-order-number-cancel').on('click', function(e){
        $('.edit-order-number-form').slideUp();
        $('#purchase-order-number').show();
        $('.edit-order-number').show();
    });


    $('.edit-order-number-form').on('submit', function(e){
        e.preventDefault();       
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });   
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            dataType: 'json',
            data: $( this ).serialize(),
            success: function (data) {
                var new_url = "{{ route('sale-orders.cart', ':order_number') }}";
                new_url = new_url.replace(':order_number', $('#order_number').val());
                $('.invalid-feedback').hide();
                $('.edit-order-number-form').slideUp();
                $('#sale-order-number').show();
                $('.order-number').html('#'+$('#order_number').val());
                $('.edit-order-number').show();
                $('#order_number').val("");
                window.history.pushState("data","Title",new_url);
            },
            error:function(xhr, textStatus, thrownError, data)
            {
                $('.invalid-feedback').show();
            }
        });     
    });


    

    </script>

@endsection
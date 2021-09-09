@extends('layouts.app')

@section('page-title')
    Purchase Order <span class="order-number">#{{ $purchase_order->order_number }}</span>
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('purchase-orders-items.store') }}" method="POST" class="form-product row">
                    <input type="hidden" name="purchase_order_id" id="purchase-order-id" value="{{ $purchase_order->id }}">
                    <input type="hidden" name="supplier_id" id="supplier-id" value="{{ $purchase_order->supplier->id }}">
                    <input type="hidden" name="warehouse_id" id="warehouse-id" value="{{ $purchase_order->warehouse->id }}">
                    
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Product</label>
                            <select class="product-select form-control" name="product_id"></select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Quantity</label>
                            <input type="quantity_ordered" class="form-control" name="quantity_ordered"  value="1">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Price</label>
                            <div class="input-group flex-nowrap">
                                <span class="input-group-text">$</span>
                                <input type="selling_price" class="form-control" name="selling_price"  id="selling_price" value="">
                                <input type="hidden" name="tax" id="tax" value="">
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
                            <table class="table table-borderless table-centered mb-0" id="purchase-order-items-table">
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
                                    @forelse($purchase_order->items as $item) 
                                        <tr class="item" data-id="{{$item->id}}">
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
                                                    <span class="input-group-text">$</span>
                                                    <input id="item-price-{{ $item->id }}" type="text" class="editable-field form-control" data-value="{{ $item->selling_price }}" data-field="price" data-item="{{ $item->id }}" placeholder="" value="{{ $item->selling_price }}">
                                                </div>
                                            </td>
                                            <td>
                                                <input id="item-quantity-{{ $item->id }}" type="number" min="1" value="{{ $item->quantity_ordered }}" class="editable-field form-control" data-value="{{ $item->quantity_ordered }}" data-field="quantity" data-item="{{ $item->id }}"
                                                    placeholder="Qty" style="width: 90px;">
                                            </td>
                                            <td>
                                                <span id="item-tax-{{ $item->id }}">{{ $item->tax }}%</span>
                                            </td>
                                            <td>
                                                <span id="item-total-{{ $item->id }}" class="item-total">${{ $item->total_price }}</span>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="action-icon" id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="no-items"><td>You haven't added any product to the purchase order, yet!</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                            
                        </div> <!-- end table-responsive-->

                    </div>
                    <!-- end col -->

                    <div class="col-lg-4">
                        <div class="border p-3 mt-4 mt-lg-0 rounded">
                            <h4 class="header-title mb-3">Order Summary <span class="order-number">#{{ $purchase_order->order_number }}</span><i class="mdi mdi-square-edit-outline ms-2 edit-order-number"></i>
                                <form class="row mt-1 edit-order-number-form" style="display:none" method="POST" action="{{ route('purchase-orders.update', $purchase_order->id) }}">
                                    @csrf()
                                    @method('PUT')
                                    <input type="hidden" name="field" value="order_number">
                                    <div class="col-xl-5">
                                        <input type="text" class="form-control col-xl-1" id="order_number" name="order_number" placeholder="" value="{{ $purchase_order->order_number }}">
                                        
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
                                            <td>Grand Total :</td>
                                            <td id="grand-total">${{ $purchase_order->amount_usd }}</td>
                                        </tr>
                                      
                                        <tr class="d-none">
                                            <td>Shipping Charge :</td>
                                            <td>${{ $purchase_order->transport_charges }}</td>
                                        </tr>
                                        <tr class="d-none">
                                            <td>Estimated Tax : </td>
                                            <td>$19.22</td>
                                        </tr>
                                        <tr>
                                            <th>Total :</th>
                                            <th>${{ $purchase_order->amount_usd }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                                
                            </div>
                            <!-- end table-responsive -->
                        </div>
                        <div class=" mt-4 mt-lg-0 rounded @if (count($purchase_order->items)==0) d-none @endif">
                            <div class="card mt-4 border">
                                <div class="card-body">
                                    <form name="place-order-form" action="{{ route('purchase-orders.ordered', $purchase_order->id) }}" method="POST">
                                        @csrf()
                                        @method('PUT')
                                        <div class="mb-3 position-relative" id="ordered_at">
                                            <label class="form-label">Ordered date</label>
                                            <input type="text" class="form-control" name="ordered_at" value="{{ $purchase_order->display_ordered_at }}"
                                            data-provide="datepicker" 
                                            data-date-container="#ordered_at"
                                            data-date-format="d-M-yyyy">
                                        </div>
                                        <button class="col-lg-12 text-center btn btn-danger" type="submit" name="place_order"><i class="mdi mdi-cart-plus me-1"></i> Place order</button>
                                    </form>

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
                <h4 class="header-title mb-3">Supplier Information</h4>
                <h5>{{ $purchase_order->supplier->company }}</h5>
                <h6>{{ $purchase_order->supplier->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    {{ $purchase_order->supplier->address }}<br>
                    @if ($purchase_order->supplier->address2)
                    {{ $purchase_order->supplier->address2 }}<br>
                    @else
                    &nbsp;<br>
                    @endif
                    {{ $purchase_order->supplier->city }}, {{ $purchase_order->supplier->zip_code }}<br/>
                    <abbr title="Phone">P:</abbr> {{ $purchase_order->supplier->phone }} <br/>
                    <abbr title="Mobile">M:</abbr> {{ $purchase_order->supplier->phone2 }} <br/>
                    <abbr title="Mobile">@:</abbr> {{ $purchase_order->supplier->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Warehouse Information <i class="mdi mdi-square-edit-outline ms-2 edit-warehouse"></i></h4>
                <form name="edit-warehouse-form" class="row mt-1 edit-warehouse-form" style="display:none" method="POST" action="{{ route('purchase-orders.update', $purchase_order->id) }}">
                    @csrf()
                    @method('PUT')
                    <input type="hidden" name="field" value="warehouse">
                    <div class="col-xl-8">
                        <select class="warehouse-select form-control" name="warehouse_id"></select>
                    </div>
                    <div class="col-xl-2">
                        <button class="btn btn-secondary" type="submit">Update</button>
                    </div>
                    <div class="col-xl-2">
                        <button class="btn btn-danger edit-warehouse-cancel" type="button">Cancel</button>
                    </div>
                </form>
                <div class="warehouse-info">
                    <h5>{{ $purchase_order->warehouse->name }}</h5>
                    <h6>{{ $purchase_order->warehouse->contact_person }}</h6>
                    <address class="mb-0 font-14 address-lg">
                        {{ $purchase_order->warehouse->address }}<br>
                        &nbsp;<br>
                        {{ $purchase_order->warehouse->city }}, {{ $purchase_order->warehouse->zip_code }}<br/>
                        <abbr title="Phone">P:</abbr> {{ $purchase_order->warehouse->phone }} <br/>
                        <abbr title="Mobile">M:</abbr> {{ $purchase_order->warehouse->phone2 }} <br/>
                        <abbr title="Email">@:</abbr> {{ $purchase_order->warehouse->email }}
                    </address>
                </div>
            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-4 d-none">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Delivery Info</h4>

                <div class="text-center">
                    <i class="mdi mdi-truck-fast h2 text-muted"></i>
                    <h5><b>UPS Delivery</b></h5>
                    <p class="mb-1"><b>Order ID :</b> xxxx235</p>
                    <p class="mb-0"><b>Payment Mode :</b> COD</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>
<!-- end row -->

<x-modal-confirm type="danger" target="item"></x-modal-confirm>

@endsection


@section('page-scripts')

    <script>

    function recalculateGrandTotal(){
        var grand_total = 0;
        $('.item-total').each(function( index ){
            grand_total = grand_total + parseFloat($(this).html().substr(1));
        });
        $('#grand-total').html('$'+grand_total.toFixed(2));
        var route = '{{ route("purchase-orders.update", ":id") }}';
        route = route.replace(':id', $('#purchase-order-id').val());
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

    var warehouseSelect = $(".warehouse-select").select2();
    warehouseSelect.select2({
        ajax: {
            url: '{{route('ajax.warehouses')}}',
            dataType: 'json'
        }
    });

    var supplierSelect = $(".supplier-select").select2();
    supplierSelect.select2({
        ajax: {
            url: '{{route('ajax.suppliers')}}',
            dataType: 'json'
        }
    });

    var productSelect = $(".product-select").select2();
    var product_route = '{{ route("ajax.products", ":supplier_id") }}';
    product_route = product_route.replace(':supplier_id', $('#supplier-id').val());
    productSelect.select2({
        ajax: {
            url: product_route,
            dataType: 'json'
        }
    });

    productSelect.on("change", function (e) { 
        var product_id = $(".product-select").find(':selected').val();
        var route = '{{ route("product.json", ":id") }}';
        route = route.replace(':id', product_id);
        $.ajax({
            type: 'GET',
            url: route,
            dataType: 'json',
            success : function(data){
                $('#selling_price').val(data.purchase_price);
                $('#tax').val(data.tax.amount);
            }
        })
    });

    function getTaxValue(tax_percentage)
    {
        return tax = 1 + (parseFloat(tax_percentage.replace('%','') / 100));
    }

    $('.editable-field ').on('blur', function(e){        
        if ($(this).val() != $(this).attr('data-value')){
            if ($(this).attr('data-field') == "price"){
                item_id = $(this).parent().parent().parent().attr('data-id');
            }
            else{
                var item_id = $(this).parent().parent().attr('data-id');
            }

            // var tax = 1 + (parseFloat($('#item-tax-' + item_id).html().replace('%','') / 100));
            var total = $('#item-price-' + item_id).val() * getTaxValue($('#item-tax-' + item_id).html()) * $('#item-quantity-' + item_id).val();
            
            $('#item-total-' + item_id).html('$'+total.toFixed(2));
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
        var route = '{{ route("purchase-orders-items.delete", ":id") }}';
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-form').attr('action', route);
        }
    });

    

    $('.form-product').on('submit', function(e){
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
                var item = '<tr class="item" data-id="'+ ['purchase_order_item_id'] +'">';
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
                                    item += '<input id="item-price-'+ data.item.purchase_order_items_id +'" type="text" class="editable-field form-control" data-value="'+ data.item.selling_price +'" data-field="price" data-item="'+ data.item.purchase_order_items_id +'" placeholder="" value="'+ data.item.selling_price  +'">';
                                    item += '</div>';
                                    item += '</td>';
                                    item += '<td>';
                                        item += '<input id="item-quantity-'+ data.item.purchase_order_items_id +'" type="number" min="1" value="'+ data.item.quantity_ordered +'" class="editable-field form-control" data-value="'+ data.item.quantity_ordered +'" data-field="quantity" data-item="'+ data.item.purchase_order_items_id +'" placeholder="Qty" style="width: 90px;">';
                        item += '</td>';
                        item += '<td>';
                        item += '<span id="item-total-'+ data.item.tax +'" class="item-total">'+ data.item.tax +'%</span>';
                        item += '</td>';
                        item += '<td>';
                        item += '<span id="item-total-'+ data.item.purchase_order_items_id +'" class="item-total">$'+((data.item.selling_price * getTaxValue(data.item.tax)) * parseInt(data.item.quantity_ordered)).toFixed(2) +'</span>';
                        item += '</td>';
                        item += '<td>';
                        item += '<a href="javascript:void(0);" class="action-icon" id="1" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>';
                        item += '</td>';
                        item += '</tr> ';
                $('#purchase-order-items-table > tbody:last-child').append(item);
                $('.no-items').remove();
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

    $('.edit-warehouse').on('click', function(e){
        $(this).hide();
        $('.edit-warehouse-form').slideDown();
        $('.warehouse-info').hide();
    });

    $('.edit-warehouse-cancel').on('click', function(e){
        $('.edit-warehouse-form').slideUp();
        $('.warehouse-info').slideDown();
        $('.edit-warehouse').show();
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
                var new_url = "{{ route('purchase-orders.cart', ':order_number') }}";
                new_url = new_url.replace(':order_number', $('#order_number').val());
                $('.invalid-feedback').hide();
                $('.edit-order-number-form').slideUp();
                $('#purchase-order-number').show();
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


    $('.edit-warehouse-form').on('submit', function(e){
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
                $('.edit-warehouse-form').slideUp();
                $('.warehouse-info').empty();
                var warehouse = "";
                warehouse +="<h5>"+data['warehouse']['name']+"</h5>";
                warehouse +="<h6>"+data['warehouse']['contact_person']+"</h6>";
                warehouse +="<address class=\"mb-0 font-14 address-lg\">"+data['warehouse']['address']+"<br>";
                    warehouse +="        &nbsp;<br>";
                    warehouse +="        "+data['warehouse']['city']+", "+data['warehouse']['zipcode']+"<br/>";
                    warehouse +="        <abbr title=\"Phone\">P:</abbr> "+data['warehouse']['phone']+" <br/>";
                    warehouse +="        <abbr title=\"Mobile\">M:</abbr> "+data['warehouse']['phone2']+" <br/>";
                    warehouse +="        <abbr title=\"Email\">@:</abbr> "+data['warehouse']['email'];
                    warehouse +="    </address>";
                $('.warehouse-info').append(warehouse).slideDown();
                $('.edit-warehouse').show();
            },
            error:function(xhr, textStatus, thrownError, data)
            {
                console.log("Error: " + thrownError);
                console.log("Error: " + textStatus);
            }
        });     
    });    

    </script>

@endsection
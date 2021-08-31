@extends('layouts.app')

@section('page-title', 'Purchase Order')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="supplier-select">Supplier</label>
                            <select class="supplier-select form-control" name="supplier_id">
                                {{-- <option value="{{$supplier->id}}" selected="selected">{{$product->supplier->company}}</option> --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Product</label>
                            <select class="product-select form-control" name="product_id">
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Quantity</label>
                            <input type="quantity" class="form-control"  value="">
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="mb-3">
                            <button type="submit" name="add-item">Add</button>
                        </div>
                    </div>
                </div>
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
                            <table class="table table-borderless table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-7">Product</th>
                                        <th class="col-2">Price</th>
                                        <th class="col-1">Quantity</th>
                                        <th class="col-2">Total</th>
                                        <th class="col-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for ($i = 0; $i<=10; $i++)
                                        <tr class="item" data-id="{{$i}}">
                                            <td>
                                                <p class="m-0 d-inline-block align-middle font-16">
                                                    <a href="apps-ecommerce-products-details.html"
                                                        class="text-body">XTM 4000-48</a>
                                                    <br>
                                                    <small class="me-2"><b>Code:</b> XTM 4000-48 </small>
                                                    <small><b>Model:</b> Xtender series
                                                    </small>
                                                </p>
                                            </td>
                                            <td>
                                                <div class="input-group flex-nowrap">
                                                    <span class="input-group-text">$</span>
                                                    <input id="item-price-{{ $i }}" type="text" class="editable-field form-control" data-value="199.99" data-field="price" data-item="{{ $i }}" placeholder="" value="{{ mt_rand(1, 100) }}.{{ mt_rand(50, 99) }}">
                                                </div>
                                            </td>
                                            <td>
                                                <input id="item-quantity-{{ $i }}" type="number" min="1" value="{{ mt_rand(1, 100) }}" class="editable-field form-control" data-value="10" data-field="quantity" data-item="{{ $i }}"
                                                    placeholder="Qty" style="width: 90px;">
                                            </td>
                                            <td>
                                                <span id="item-total-{{ $i }}" class="item-total">${{ mt_rand(100, 1000) }}.{{ mt_rand(50, 100) }} </span>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="action-icon" id="1" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>
                                            </td>
                                        </tr>
                                    @endfor
                                   
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <!-- action buttons-->
                        <div class="row mt-4 d-none">
                            <div class="col-sm-12">
                                <div class="text-sm-end">
                                    <a href="apps-ecommerce-checkout.html" class="btn btn-danger">
                                        <i class="mdi mdi-cart-plus me-1"></i> Save </a>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row-->
                    </div>
                    <!-- end col -->

                    <div class="col-lg-4">
                        <div class="border p-3 mt-4 mt-lg-0 rounded">
                            <h4 class="header-title mb-3">Order Summary</h4>

                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td>Grand Total :</td>
                                            <td id="grand-total">$1571.19</td>
                                        </tr>
                                      
                                        <tr>
                                            <td>Shipping Charge :</td>
                                            <td>$25</td>
                                        </tr>
                                        <tr>
                                            <td>Estimated Tax : </td>
                                            <td>$19.22</td>
                                        </tr>
                                        <tr>
                                            <th>Total :</th>
                                            <th>$1458.3</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->
                        </div>

                    </div> <!-- end col -->

                </div> <!-- end row -->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
<!-- end row -->

<x-modal-confirm type="danger" target="item"></x-modal-confirm>

@endsection


@section('page-scripts')

    <script>
        var supplierSelect = $(".supplier-select").select2();
        supplierSelect.select2({
            ajax: {
                url: '{{route('ajax.suppliers')}}',
                dataType: 'json'
            }
        });

        var productSelect = $(".product-select").select2();
        var product_route = '{{ route("ajax.products", ":id") }}';
        // product_route = product_route.replace(':id', $('.supplier-select').val());
        product_route = product_route.replace(':id', 1)
        productSelect.select2({
            ajax: {
                url: product_route,
                dataType: 'json'
            }
        });


        $('.editable-field ').on('blur', function(e){        
            if ($(this).val() != $(this).attr('data-value')){
                // Update product total on screen
                var item_id = $(this).parent().parent().attr('data-id');
                var total = $('#item-price-' + item_id).val() * $('#item-quantity-' + item_id).val();
                $('#item-total-' + item_id).html('$'+total);

                // Update purchase order item
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    }
                });
                var route = '{{ route("purchase-orders-items", ":id") }}';
                route = route.replace(':id', item_id);

                $.ajax({
                    type: 'POST',
                    url: route,
                    dataType: 'json',
                    data: { 
                        'value' : $(this).val() , 
                        'field': $(this).attr('data-field'), 
                        'item': $(this).attr('data-item')
                    },
                    success : function(result){
                        $(this).attr('data-value') == $(this).val();
                    }
                });
            }

            // Update grand total amount
            var grand_total = 0;
            $('.item-total').each(function( index){
                grand_total = grand_total + parseInt($(this).html().substr(1));
            });
            $('#grand-total').html('$'+grand_total);
        });


        $('#delete-modal').on('show.bs.modal', function (e) {
            var route = '{{ route("purchase-orders-items", ":id") }}';
            var button = e.relatedTarget;
            if (button != null){
                route = route.replace(':id', button.id);
                $('#delete-form').attr('action', route);
            }
        });

    </script>

@endsection
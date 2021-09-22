@extends('layouts.app')

@section('page-title', 'Inventory Movement')

@section('page-style')

<style type="text/css">
    .hlclass {
        background-color: orange !important;
    }
</style>

@endsection


@section('content')

<div class="row">
    <div class="col-xxl-8 col-lg-6">
        <!-- project card -->
        <div class="card d-block">
            <div class="card-body">

                    <div class="col-lg-3">
                        <div class="mb-3">
                            <label class="form-label" for="product-select">Product</label>
                            <select class="product-select form-control" name="product_id" id="product_id"></select>
                            <input type="hidden" id="hidden_product_id" value=" {{ $product->id ?? '' }} ">
                        </div>
                    </div>

                <!-- project title-->
                <h3 class="mt-0">
                    {{ $product->name ?? '' }}
                </h3>

                <h5>Description:</h5>

                <p class="text-muted mb-3">
                    {{$product->notes ?? ''}}
                </p>

            </div> <!-- end card-body--> 
        </div> <!-- end card-->
    </div> <!-- end col -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Supplier Information</h4>
                <h5>{{ $product->supplier->company  ?? '' }}</h5>
                <h6>{{ $product->supplier->contact_person  ?? ''}}</h6>
                <address class="mb-0 font-14 address-lg">
                    {{ $product->supplier->address  ?? ''}}<br>
                    {{ $product->supplier->address2  ?? ''}}<br>
                    {{ $product->supplier->city  ?? ''}}, {{ $product->supplier->zip_code  ?? ''}}<br/>
                    <abbr title="Phone">P:</abbr> {{ $product->supplier->phone  ?? ''}} <br/>
                    <abbr title="Mobile">M:</abbr> {{ $product->supplier->phone2  ?? ''}} <br/>
                    <abbr title="Email">@:</abbr> {{ $product->supplier->email  ?? ''}}
                </address>
            </div>
        </div>
    </div> <!-- end col -->
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-10">
                        filters: start_period | end_period | “All / Purchase Orders / Sales Orders”
                    </div>
                    <div class="col-sm-2">
                        <div class="text-sm-end">
                           <button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button> 
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="inventory-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Order #</th>
                                <th>Quantity</th>
                                <th>Entry</th>
                                <th>Warehouse</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


<x-modal-confirm type="danger" target="category"></x-modal-confirm>


@endsection





@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";



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
        table.ajax.reload();
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


    var table = $('#inventory-datatable').DataTable({
        processing: true,
        serverSide: true,
        deferLoading: 0,
        searching: false,
        paging: false,
        ajax      : {
            url   : "{{ route('inventory-movement.datatables') }}",
            "data": function ( d ) {
                d.filter_product_id = $(" #hidden_product_id ").val();
            },
        }, 
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing inventory _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> rows'
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "columns": [
            { 
                'data': 'created_at',
                'orderable': true 
            },
            { 
                'data': 'order_number',
                'orderable': true 
            },
            { 
                'data': 'quantity',
                'orderable': false
            },
            { 
                'data': 'entry_type',
                'orderable': false
            },
            { 
                'data': 'warehouse',
                'orderable': false
            },
            { 
                'data': 'user',
                'orderable': false
            }
        ],
        
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#inventory-datatable_length label').addClass('form-label');
            
        },
        
    });

console.log("id" + $( "#hidden_product_id" ).val());

    if ( $( "#hidden_product_id" ).val().length != 0)
        table.ajax.reload();

    @if(Session::has('success'))
        $.NotificationApp.send("Success","{{ session('success') }}","top-right","","success")
    @endif
    @if(Session::has('error'))
        $.NotificationApp.send("Error","{{ session('error') }}","top-right","","error")
    @endif


});

    </script>    
@endsection

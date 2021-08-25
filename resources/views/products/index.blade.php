@extends('layouts.app')

@section('page-title', 'Products')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <a href="{{ route('products.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> Add Products</a>
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <a class="btn" href="{{ route('export.products') }}"><button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button></a>
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="products-datatable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheck1">
                                        <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                    </div>
                                </th>
                                <th>Category</th>
                                <th>Supplier</th> 
                                <th>Tax</th>
                                <th>Code</th> 
                                <th>Name</th> 
                                <th>Model</th> 
                                <th>Cable length</th> 
                                <th>KW rating</th> 
                                <th>Part number</th> 
                                {{-- <th>Notes</th>  --}}
                                <th>Actions</th>
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


<x-modal-confirm type="danger" target="product"></x-modal-confirm>


@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";


    // Default Datatable
    var table = $('#products-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('products.datatables') }}",


        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing Products _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> Products',
        },
        "pageLength": 10,
        "columns": [
            {
                'data': 'id',
                'orderable': false,
                'render': function (data, type, row, meta) {
                    if (type === 'display') {
                        data = "<div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input dt-checkboxes\"><label class=\"form-check-label\">&nbsp;</label></div>";
                    }
                    return data;
                },
                'checkboxes': {
                    'selectRow': true,
                    'selectAllRender': '<div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input dt-checkboxes\"><label class=\"form-check-label\">&nbsp;</label></div>'
                }
            },
            { 
                'data': 'category',
                'orderable': true 
            },
            { 
                'data': 'supplier',
                'orderable': true 
            },
            { 
                'data': 'tax',
                'orderable': true 
            },
            { 
                'data': 'code',
                'orderable': true 
            },
            { 
                'data': 'name',
                'orderable': true 
            },
            { 
                'data': 'model',
                'orderable': true 
            },
            { 
                'data': 'cable_length',
                'orderable': true 
            },
            { 
                'data': 'kw_rating',
                'orderable': true 
            },
            { 
                'data': 'part_number',
                'orderable': true 
            },
            // { 
            //     'data': 'notes',
            //     'orderable': true 
            // },
            {
                'data': 'id',
                'orderable': false,
                'render' : function(data, type, row, meta){
                    if (type === 'display'){

                        var edit_btn = '';
                        var delete_btn = '';

                        @if (Auth::user()->can('edit products'))
                            var edit_route = '{{ route("products.edit", ":id") }}';
                            edit_route = edit_route.replace(':id', data);
                            edit_btn = '<a href="' + edit_route + '" class="action-icon"> <i class="mdi mdi-pencil"></i></a>'                       
                        @endif
                        @if (Auth::user()->can('delete products'))
                            delete_btn = '<a href="" class="action-icon" id="' + data + '" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>'
                        @endif

                        data = edit_btn +  delete_btn
                    }
                    return data;
                }
            }
            
        ],
        "select": {
            "style": "multi"
        },
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#products-datatable_length label').addClass('form-label');
            
        },
    });

    $('#products-datatable').on('dblclick', 'tr', function () {
        var route = '{{  route("products.show", ":id") }}';
        route = route.replace(':id', table.row( this ).data().id);
        window.location.href = route;
    });


    $('#delete-modal').on('show.bs.modal', function (e) {
        var route = '{{ route("products.delete", ":id") }}';
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-form').attr('action', route);
        }
        
    });


    @if(Session::has('success'))
        $.NotificationApp.send("Success","{{ session('success') }}","top-right","","success")
    @endif
    @if(Session::has('error'))
        $.NotificationApp.send("Error","{{ session('error') }}","top-right","","error")
    @endif
  
});


    
    </script>    
@endsection

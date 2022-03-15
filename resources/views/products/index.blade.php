@extends('layouts.app')

@section('title')
    @parent() | Products
@endsection

@section('page-title', 'Products')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        @if (Auth::user()->can('edit products'))
                            <a href="{{ route('products.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> {{ __('app.add_title', ['field' => 'product']) }}</a>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <a class="btn toggle-filters" href="javascript:void(0);"><button type="button" class="btn btn-light mb-2"><i class="mdi mdi-filter"></i></button></a>
                            <a class="btn" href="{{ route('export.products') }}"><button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button></a>
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="products-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Supplier</th> 
                                <th>Part Number</th> 
                                <th>Purchase Price</th>
                                <th>Tax</th>
                                <th>Actions</th>
                            </tr>
                            <tr class="filters" style="display:none;">
                                <th><th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="no-filter"></th>
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

    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });

    // Default Datatable
    var table = $('#products-datatable').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        fixedHeader: true,
        search: true,
        
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
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "columns": [
            { 
                'data': 'category',
                'orderable': true 
            },
            { 
                'data': 'supplier',
                'orderable': true 
            },
          
            { 
                'data': 'part_number',
                'orderable': true 
            },
            {
                'data': 'purchase_price',
                'orderable': true,
            },
            { 
                'data': 'tax',
                'orderable': true,
                'render': function(data, type, row, meta){
                    if (type === 'display'){
                        return data + '%';
                    }
                    return data;
                }
            },
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
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#products-datatable_length label').addClass('form-label');
            
        },
    });

    table.columns().eq(0).each(function(colIdx) {
        var cell = $('.filters th').eq($(table.column(colIdx).header()).index());
        var title = $(cell).text();

        if($(cell).hasClass('no-filter')){

            $(cell).html('&nbsp');

        }
        else{

            $(cell).html( '<input class="form-control filter-input" type="text"/>' );

            $('input', $('.filters th').eq($(table.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                //var regexr = '({search})'; //$(this).parents('th').find('select').val();
                table
                    .column(colIdx)
                    .search(this.value) //(this.value != "") ? regexr.replace('{search}', 'this.value') : "", this.value != "", this.value == "")
                    .draw();
                 
            });            
        }
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

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
                            <button type="button" class="btn btn-success mb-2 me-1"><i class="mdi mdi-cog"></i></button>
                            <button type="button" class="btn btn-light mb-2 me-1">Import</button>
                            <button type="button" class="btn btn-light mb-2">Export</button>
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
                                <th>Notes</th> 
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


@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    var data = [{
        "id": 1,
        "category": "Inverter",
        "supplier": "Studer",
        "tax": "12.00",
        "code": 'CD02',
        "name": "Inverter 4KW",
        "model": 'A1Z2',
        "cable_length": "5m",
        "kw_rating": "4",
        "part_number": "AQSZED",
        "notes": "this is a note"
      }, {
        "id": 2,
        "category": "Solar Panel",
        "supplier": "Wairee",
        "tax": "5.00",
        "code": "CD01",
        "name": "Solar Panel",
        "model": 'SP390',
        "cable_length": "0",
        "kw_rating": "390",
        "part_number": "11SPWA",
        "notes": "this is a note"
      }
    ];    



    // Default Datatable
    var table = $('#products-datatable').DataTable({
//        "data": data,
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
            { 
                'data': 'notes',
                'orderable': true 
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
  
});


    
    </script>    
@endsection

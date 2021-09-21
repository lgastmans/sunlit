@extends('layouts.app')

@section('page-title', 'Inventory')

@section('page-style')

<style type="text/css">
    .hlclass {
        background-color: orange !important;
    }
</style>

@endsection


@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                           <button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button> 
                        </div>
                    </div><!-- end col-->
                </div>

{{--                         <div class="dropdown-menu" id="dropdown-inventory-filter">
                            <a class="dropdown-item" id="__ALL_"  href="#">All</a>
                            <a class="dropdown-item" id="__BELOW_MIN_"  href="#">Below Minimum</a>
                            <a class="dropdown-item" id="__NONE_ZERO_"  href="#">Non-zero</a>
                            <a class="dropdown-item" id="__ZERO_"  href="#">Zero</a>
                        </div>
 --}}                    
                    <input type="hidden" id="dropdown-inventory-filter" value="__BELOW_MIN_" />

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="inventory-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Category</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Available Stock</th>
                                <th>Ordered Stock</th>
                                <th>Booked Stock</th>
                                <th>Projected Stock</th>
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

    var table = $('#inventory-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax      : 
            {
                url   : "{{ route('inventory.datatables') }}",
                "data": function ( d ) {
                    d.filterMinQty = "__BELOW_MIN_"; //$(" #dropdown-inventory-filter a ").attr('id');
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
                '</select> rows',
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "createdRow": function( row, data, dataIndex, cells ) {
            if (data.available.scalar <= data.minimum_quantity.scalar) {
                $('td', row).eq(5).html('<span class="badge badge-danger-lighten">'+data.available.scalar +'</span>');
            }
        },
        "columns": [
            { 
                'data': 'supplier',
                'orderable': true 
            },
            { 
                'data': 'warehouse',
                'orderable': true 
            },
            { 
                'data': 'category',
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
                "data": 'available',
                'orderable': false,
                "render" : function ( data, type, row ) {
                    return data.scalar;
                },
            },
            { 
                'data': 'ordered',
                'orderable': false
            },
            { 
                'data': 'booked',
                'orderable': false
            },
            { 
                'data': 'projected',
                'orderable': false
            },
        ],
        
        // "select": {
        //     "style": "multi"
        // },
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#inventory-datatable_length label').addClass('form-label');
            
        },
        
    });

    $(" #dropdown-inventory-filter a ").on("click", function() {

        var sel = $(this).attr('id');

        // console.log('selected value: ' + sel);

        if (sel=="__ALL_") {
          $(" #dropdownMenuButton ").removeClass( "btn-success" ).addClass( "btn-secondary" );
          $(" #__ALL_ ").addClass("active");
          $(" #__BELOW_MIN_ ").removeClass("active");
          $(" #__NONE_ZERO_ ").removeClass("active");
          $(" #__ZERO_ ").removeClass("active");
        }
        else
          $(" #dropdownMenuButton ").removeClass( "btn-secondary" ).addClass( "btn-success" );

        if (sel=="__BELOW_MIN_") {
          $(" #__ALL_ ").removeClass("active");
          $(" #__BELOW_MIN_ ").addClass("active");
          $(" #__NONE_ZERO_ ").removeClass("active");
          $(" #__ZERO_ ").removeClass("active");
        }
        else if (sel=="__NONE_ZERO_") {
          $(" #__ALL_ ").removeClass("active");
          $(" #__BELOW_MIN_ ").removeClass("active");
          $(" #__NONE_ZERO_ ").addClass("active");
          $(" #__ZERO_ ").removeClass("active");
        }
        else if (sel=="__ZERO_") {
          $(" #__ALL_ ").removeClass("active");
          $(" #__BELOW_MIN_ ").removeClass("active");
          $(" #__NONE_ZERO_ ").removeClass("active");
          $(" #__ZERO_ ").addClass("active");
        }

        table.ajax.reload();

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

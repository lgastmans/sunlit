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


                <div class="table-responsive">
                    <table class="table table-centered table-striped table-bordered table-hover w-100 dt-responsive nowrap table-has-dlb-click" id="inventory-datatable">
                        <thead class="table-light">
                            <tr>

                                <th>Warehouses</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Part Number</th>
                                <th>Available</th>
                                <th>Ordered</th>
                                <th>Blocked</th>
                                <th>Booked</th>
                                <th>Projected</th>
                            </tr>
                            <tr class="filters" style="display: none;">
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                {{-- <th><input type="text" class="form-control"></th> --}}
                                <th><select class="form-control available-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                <th><select class="form-control ordered-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                <th><select class="form-control blocked-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                <th><select class="form-control booked-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                <th><select class="form-control projected-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                {{-- <th class="no-filter"><select disabled class="form-control projected-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th> --}}
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

    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });

    var table = $('#inventory-datatable').DataTable({
        dom: 'Bfrtip',
        stateSave: true,
        scrollY: "500px",
        paging: false,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                },
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
                },
                className: 'btn btn-warning',
                download: 'open'
            },
            {
                extend: 'colvis',
                columns: ':not(.noVis)',
                className: 'btn btn-info'
            },
            {
                text: '<i class="mdi mdi-filter"></i>&nbsp;Filter',
                // className: 'btn btn-light',
                action: function ( e, dt, node, config ) {
                    $( ".filters" ).slideToggle('slow');
                }
            }
        ],
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        fixedHeader: true,
        ajax      : 
            {
                url   : "{{ route('inventory.datatables') }}",
                "data": function ( d ) {[
                    d.filter_available = $(" .available-filter ").val(),
                    d.filter_ordered = $(" .ordered-filter ").val(),
                    d.filter_blocked = $(" .blocked-filter ").val(),
                    d.filter_booked = $(" .booked-filter ").val(),
                    d.filter_projected = $(" .projected-filter ").val()
                ]},
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
        "createdRow": function( row, data, dataIndex, cells ) {
            if (data.available.scalar <= data.minimum_quantity.scalar) {
                $('td', row).eq(4).html('<span class="badge badge-danger-lighten">'+data.available.scalar +'</span>');
            }
        },
        "columns": [
            { 
                'data': 'warehouse',
                'orderable': true 
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
                'data': 'part_number',
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
                'data': 'blocked',
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
        "order": [[3, "asc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#inventory-datatable_length label').addClass('form-label');
            
        },
        
    });

/*
    $(" .available-filter, .ordered-filter, .booked-filter, .projected-filter ").on("change", function() {
        table.ajax.reload();
    });

    $('.filters th input').on("keyup", function() {
        console.log('here');
        table.ajax.reload();
    });
*/

    table.columns().eq(0).each(function(colIdx) {
        var cell = $('.filters th').eq($(table.column(colIdx).header()).index());
        var title = $(cell).text();

        if($(cell).hasClass('no-filter')){

            $(cell).html('&nbsp');

        }
        else{

            // $(cell).html( '<input class="form-control filter-input" type="text"/>' );

            $('select', $('.filters th').eq($(table.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                //var regexr = '({search})'; //$(this).parents('th').find('select').val();
                table
                    .column(colIdx)
                    .search(this.value) //(this.value != "") ? regexr.replace('{search}', 'this.value') : "", this.value != "", this.value == "")
                    .draw();
                 
            });
            
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


    $(" #inventory-datatable ").on('dblclick', 'tr', function () {
        var route = '{{  route("products.show", ":id") }}';
        route = route.replace(':id', table.row( this ).data().product_id);
        window.location.href = route;
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

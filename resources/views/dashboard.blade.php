@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')


<div class="row">
    <div class="col-lg-2">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-cart-plus widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Due Pending Orders</h5>
                <a href="{{ route('purchase-orders.filtered', 'due') }}">
                    <h3 class="mt-3 mb-3 @if (count($due_orders) < 5 ) text-success @elseif (count($due_orders) < 10) text-warning @else text-danger @endif">{{ count($due_orders) }}</h3>
                </a>
                <p class="mb-0 text-muted">
                    <span class="text-danger me-2"></span>
                    <span class="text-nowrap"></span>
                </p>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
    <div class="col-lg-2">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-cart-plus widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Overdue Pending Orders</h5>
                <a href="{{ route('purchase-orders.filtered', 'overdue') }}">
                    <h3 class="mt-3 mb-3 @if ($overdue_orders < 5 ) text-success @elseif ($overdue_orders < 10) text-warning @else text-danger @endif">{{ $overdue_orders }}</h3>
                </a>
                <p class="mb-0 text-muted">
                    <span class="text-danger me-2"></span>
                    <span class="text-nowrap"></span>
                </p>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
    <div class="col-lg-2 offset-lg-6">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-currency-inr widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Growth">Current exchange rate</h5>
                <h3 class="mt-3 mb-3 text-warning">{{ __('app.currency_symbol_inr')}} {{ number_format(\Setting::get('purchase_order.exchange_rate'),2) }}</h3>
                <p class="mb-0 text-muted">
                    @if (str_contains($exchange_rate_update_ago, 'days'))
                        <a href="javascript:void(0);" class="update-echange-rate">
                            <span class="text-success me-2"><i class="mdi mdi-refresh"></i> last update</span>
                        </a>
                    @else
                        <span class="text-success me-2"><i class="mdi mdi-refresh"></i> last update</span>
                    @endif
                    <span id="currency-rate" class="text-nowrap"><a href="#" data-bs-container="#currency-rate" data-bs-toggle="tooltip" title="{{ \Carbon\Carbon::parse( \Setting::get('exchange_rate_updated_at') )->toDayDateTimeString() }}">{{ $exchange_rate_update_ago }}</a></span>
                </p>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <h3 class="pb-1">Products with negative stock</h3>
                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap table-has-dlb-click" id="inventory-datatable">
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
                                <th><select class="form-control projected-filter">@foreach($stock_filter as $k => $v) <option value={{ $k }} >{{ $v }}</option> @endforeach</select></th>
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
<!-- end row -->


@endsection

@section('page-scripts')

    <script>
        
 $(document).ready(function () {
    "use strict";
    var pre_filter_projected = true;

    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });

    $('.projected-filter').on('change', function(e){
        pre_filter_projected = false;
    });

    if (pre_filter_projected === false) {
        var projected_filter = $(" .projected-filter ").val();
    }
    else{
        $('.projected-filter').val('__ZERO_');
        var projected_filter = "__ZERO_";
    }
    var table = $('#inventory-datatable').DataTable({
        dom: 'Bfrtip',
        stateSave: true,
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
                    d.filter_projected = projected_filter
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

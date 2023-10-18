@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')


<!--

    Current exchange rate

 -->
<div class="row">
    <div class="col-xl-3 col-lg-4 ">
        <div class="card cta-box overflow-hidden">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-currency-usd widget-icon"></i>
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
            </div>
            <!-- end card-body -->
        </div>
    </div> <!-- end col -->

{{--
    <div class="col-lg-4">
        <div class="card cta-box">
            <div class="d-flex card-header justify-content-between align-items-center">
                <h4 class="header-title">Revenue By Location</h4>
            </div>

            <div class="card-body pt-0">
                <div class="mb-4 mt-3">
                    <!-- <div id="world-map-markers" style="height: 217px"></div> -->
                    <div id="india-vectormap" style="height: 217px"></div>
                </div>    
            </div>
        </div>
    </div>
--}}
{{-- 
    <div class="col-xl-5 col-lg-6">
       <div class="row">
            <div class="col-sm-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-account-multiple widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Customers</h5>
                        <h3 class="mt-3 mb-3">36,254</h3>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 5.27%</span>
                            <span class="text-nowrap">Since last month</span>  
                        </p>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-sm-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-cart-plus widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Orders</h5>
                        <h3 class="mt-3 mb-3">5,543</h3>
                        <p class="mb-0 text-muted">
                            <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 1.08%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row -->

        <div class="row">
            <div class="col-sm-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-currency-usd widget-icon bg-success-lighten text-success"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Revenue</h5>
                        <h3 class="mt-3 mb-3">$6,254</h3>
                        <p class="mb-0 text-muted">
                            <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 7.00%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-sm-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-pulse widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Growth">Growth</h5>
                        <h3 class="mt-3 mb-3">+ 30.56%</h3>
                        <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 4.87%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row -->
    </div> <!-- end col -->
--}}

</div>


<ul id="nav-dashboard" class="nav nav-tabs nav-bordered mb-3">
    <li class="nav-item">
        <a href="#" data-related="dashboard-category" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
            Category-wise Sales Totals
        </a>
    </li>
    <li class="nav-item">
        <a href="#" data-related="dashboard-inventory" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
            Inventory
        </a>
    </li>
    <li class="nav-item">
        <a href="#" data-related="dashboard-product" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
            Product-wise Sales Totals
        </a>
    </li>
    <li class="nav-item">
        <a href="#" data-related="dashboard-state" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
            State-wise Sales Totals
        </a>
    </li>
</ul> <!-- end nav-->

<div class="tab-content">

    <div class="tab-pane show active" id="dashboard-category">
        <!-- 
            Category-wise Sales Totals 
        -->
        @if ($userrole === 'Super-admin')
        <div class="row">
            <div class="col-xl-12 col-lg-12 ">
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <h4>Category-wise Sales Totals</h4>
                        <div class="row justify-content-start">
                            <span class="col-1 pt-1">Period</span>
                            <div class="col-2">
                                <select class="period-select form-control" name="period_id" id="select_period">
                                    <option value="period_monthly" selected>Monthly</option>
                                    <option value="period_quarterly">Quarterly</option>
                                </select>
                            </div>
                            
                            <div class="col-2" id="display_monthly_year">
                                <input type="text" class="form-control " id="year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                                data-date-container="#display_monthly_year">
                            </div>  
                        </div>              
                    </div>
                    <div class="card-body" id="table-sales-tables-container"></div>
                </div>

            </div>
        </div>
        @endif
    </div> <!-- dashboard-category -->


    <div class="tab-pane show" id="dashboard-inventory">
        <!--
            Inventory 
        -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <h4 class="header-title mb-3">Inventory</h4><h5>Listed by highest selling products first</h5>
                            <p><small>
                                The <strong>Available</strong> column shows the actual, physical stock, regardless of the <strong>Blocked</strong> or <strong>Booked</strong> columns.<br>
                                The <strong>Blocked</strong> and <strong>Booked</strong> columns only affect the <strong>Projected</strong> column. The <strong>Projected</strong> column is calculated as <i>Projected = Available + Ordered - Booked</i>
                            </small></p>
                            <div class="table-responsive">
                                <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap table-has-dlb-click" id="inventory-datatable">
                                    <thead>
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
                                            <th>Total Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
    </div> <!-- dashboard-inventory -->


    <div class="tab-pane show" id="dashboard-product">
        <!-- 
            Product-wise Sales Totals 
        -->
        @if ($userrole === 'Super-admin')
        <div class="row">
            <div class="col-xl-12 col-lg-12 ">

                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <h4>Product-wise Sales Totals <small>(Top 5 products)</small></h4>

                        <div class="row justify-content-start">
                            <span class="col-1 pt-1">Year</span>
                            <div class="col-2" id="display_product_year">
                                <input type="text" class="form-control " id="product_year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                                data-date-container="#display_product_year">
                            </div>  
                        </div>              
                        <div class="table-responsive">
                            <table id="table-product-sales" class="table table-striped table-condensed" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Part Number</th>
                                        <th style="text-align: right;">January</th>
                                        <th style="text-align: right;">February</th>
                                        <th style="text-align: right;">March</th>
                                        <th style="text-align: right;">April</th>
                                        <th style="text-align: right;">May</th>
                                        <th style="text-align: right;">June</th>
                                        <th style="text-align: right;">July</th>
                                        <th style="text-align: right;">August</th>
                                        <th style="text-align: right;">September</th>
                                        <th style="text-align: right;">October</th>
                                        <th style="text-align: right;">November</th>
                                        <th style="text-align: right;">December</th>
                                        <th style="text-align: right;">Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> {{-- table-responsive --}}
                    </div>
                </div>

            </div>
        </div>
        @endif
    </div> <!-- dashboard-product -->


    <div class="tab-pane show" id="dashboard-state">
        <!--
            State-wise Sales Totals 
        -->
        @if ($userrole === 'Super-admin')
        <div class="row">
            <div class="col-xl-12 col-lg-12 ">

                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <h4>State-wise Sales Totals</h4>
                        <div class="row justify-content-start">
                            <span class="col-1 pt-1">Period</span>
                            <div class="col-2">
                                <select class="period-state form-control" name="state_period_id" id="select_state_period">
                                    <option value="period_monthly" selected>Monthly</option>
                                    <option value="period_quarterly">Quarterly</option>
                                </select>
                            </div>
                            <div class="col-2" id="display_state_year">
                                <input type="text" class="form-control " id="state_year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                                data-date-container="#display_state_year">
                            </div>                
                                
                        </div>
                    </div>
                    <div class="card-body" id="table-state-tables-container"></div>
                </div>

            </div>
        </div>
        @endif
    </div> <!-- dashboard-state -->

</div> <!-- tab-content -->



@endsection

@section('page-scripts')
        
<!-- Vector Map js -->
<!-- <script src="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="assets/vendor/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js"></script> -->

<script>

$(document).ready(function () {
    "use strict";

    // $("#nav-dashboard li").on("click", function(){
    //     console.log($(this).val());
    // });

    $("#nav-dashboard li a").on("click", function() {
        $("div[id=dashboard-category]").hide();
        $("div[id=dashboard-inventory]").hide();
        $("div[id=dashboard-product]").hide();
        $("div[id=dashboard-state]").hide();
        $("div[id=" + $(this).attr("data-related") + "]").show();
        
    });    

    drawSalesTotals();
    drawStateTotals();

    /**
     * Product-wise Sales Totals
     * 
    */
    $('#product_year_id').datepicker({
        format: 'yyyy',
        minViewMode: 2,
        maxViewMode: 2,
        autoclose: true,
    })
    .on("change", function() {
        productSalesTable.ajax.reload();
    });

    var productSalesTable = $('#table-product-sales').DataTable({
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        fixedHeader: true,
        //deferLoading: 0,
        searching: true,
        paging: false,
        ajax      : {
            url   : "{{ route('ajax.sales-product-totals') }}",
            "data": function ( d ) {
                d.limit = 5,
                d.year = $(" #product_year_id ").val()
            },
        }, 
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> rows'
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "columns": [
            { 
                'data': 'part_number',
                'orderable': false 
            },
            { 
                'data': 'jan',
                'orderable': false
            },
            { 
                'data': 'feb',
                'orderable': false
            },
            { 
                'data': 'mar',
                'orderable': false
            },
            { 
                'data': 'apr',
                'orderable': false
            },
            { 
                'data': 'may',
                'orderable': false
            },
            { 
                'data': 'jun',
                'orderable': false 
            },
            { 
                'data': 'jul',
                'orderable': false 
            },
            { 
                'data': 'aug',
                'orderable': false 
            },
            { 
                'data': 'sep',
                'orderable': false 
            },
            { 
                'data': 'oct',
                'orderable': false 
            },
            { 
                'data': 'nov',
                'orderable': false 
            },
            { 
                'data': 'dec',
                'orderable': false 
            },
            { 
                'data': 'total',
                'orderable': false 
            },
        ],
        columnDefs: [
            { className: "dt-right", "targets": [1,2,3,4,5,6,7,8,9,10,11,12,13] },   //'_all' }
        ],            
        "aaSorting": [[13, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#sale-orders-datatable_length label').addClass('form-label');
 
        },    
    });



    /**
     * Category-wise Sales Totals
     * 
     */
    $(" #select_period ").on("change", function() {
        drawSalesTotals();
    });

    $('#year_id').datepicker({
        format: 'yyyy',
        minViewMode: 2,
        maxViewMode: 2,
        autoclose: true,
    })
    .on("change", function() {
        drawSalesTotals();
    });   

    function drawSalesTotals()
    {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var route = '{{ route("ajax.sales-totals") }}';

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'html',
            data: { 
                'period': $(" #select_period ").val(),
                'year': $(" #year_id ").val(),
                'month': '_ALL',
                'quarter': '_ALL',
                'category': '',
                '_method': 'GET'
            },
            success : function(result)
            {
                $("#table-sales-tables-container").html(result);
            }
        });
    }


    /**
     * State-wise Sales Totals
     * 
     */
    $(" #select_state_period ").on("change", function() {
        drawStateTotals();
    });

    $('#state_year_id').datepicker({
        format: 'yyyy',
        minViewMode: 2,
        maxViewMode: 2,
        autoclose: true,
    })
    .on("change", function() {
        drawStateTotals();
    }); 

    function drawStateTotals()
    {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var route = "{{ route('ajax.sales-state-totals') }}";

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'html',
            data: { 
                'period': $(" #select_state_period ").val(),
                'year': $(" #state_year_id ").val(),
                'month': '_ALL',
                'quarter': '_ALL',
                'state': '',
                '_method': 'GET'
            },
            success : function(result)
            {
                $("#table-state-tables-container").html(result);
            }
        });
    }

    /**
     *  Inventory
     * 
     */
    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });

    var table = $('#inventory-datatable').DataTable({
        dom: 'frtip',
        stateSave: true,
        // buttons: [
        //     {
        //         extend: 'excelHtml5',
        //         exportOptions: {
        //             columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
        //         },
        //         className: 'btn btn-success'
        //     },
        //     {
        //         extend: 'pdfHtml5',
        //         exportOptions: {
        //             columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ]
        //         },
        //         className: 'btn btn-warning',
        //         download: 'open'
        //     },
        //     {
        //         extend: 'colvis',
        //         columns: ':not(.noVis)',
        //         className: 'btn btn-info'
        //     },
        //     {
        //         text: '<i class="mdi mdi-filter"></i>&nbsp;Filter',
        //         // className: 'btn btn-light',
        //         action: function ( e, dt, node, config ) {
        //             $( ".filters" ).slideToggle('slow');
        //         }
        //     }
        // ],
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        fixedHeader: true,
        scrollY: "500px",
        paging: false,        
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
            { 
                'data': 'total_sales',
                'orderable': false
            },            
        ],
        
        // "select": {
        //     "style": "multi"
        // },
        "order": [[3, "asc"]],
        columnDefs: [
            { className: "dt-right", "targets": [4,5,6,7,8,9] },   //'_all' }
        ],        
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#inventory-datatable_length label').addClass('form-label');
            
        },
        
    });

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

@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')

<div class="row">
    <div class="col-xl-12 col-lg-12 ">

        <div class="card border-primary mb-3">
            <div class="card-header">
                <h4>Monthly Sales Totals</h4>
            </div>
            <div class="card-body">

                <div class="col-xl-1">
                    <label class="form-label" for="period-select">Period</label>
                    <select class="period-select form-control" name="period_id" id="select_period">
                        <option value="period_monthly" selected>Monthly</option>
                        <option value="period_quarterly">Quarterly</option>
                        <!-- <option value="period_yearly">Yearly</option> -->
                    </select>
                </div>
                
                <div class="col-xl-1 position-relative " id="display_monthly_year">
                    <label class="form-label">&nbsp;</label>
                    <input type="text" class="form-control " id="year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                    data-date-container="#display_monthly_year">
                </div>                

                <div class="table-responsive">
                    <table id="table-sales-totals" class="table table-striped table-condensed" cellspacing="0" width="100%">
                        <thead id="table-sales-totals-thead">
                            <tr>
                                <th>Category</th>
                                <th>January</th>
                                <th>February</th>
                                <th>March</th>
                                <th>April</th>
                                <th>May</th>
                                <th>June</th>
                                <th>July</th>   
                                <th>August</th>
                                <th>September</th>
                                <th>October</th>
                                <th>November</th>
                                <th>December</th>
                            </tr>
                        </thead>
                        <tbody id="table-sales-totals-tbody">
                            @foreach ($sale_order_totals as $category_label=>$category)
                                <tr>
                                    <td>{{ $category_label }}</td>

                                    @foreach ($category as $key=>$month)
                                        <td>{{ $month['total_amount'] }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> {{-- table-responsive --}}

            </div> {{-- card-body --}}
        </div>

    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-4 ">
        <div class="card tilebox-one d-none">
            <div class="card-body">
                <i class='uil uil-users-alt float-right'></i>
                <h6 class="text-uppercase mt-0">Active Users</h6>
                <h2 class="my-2" id="active-users-count">121</h2>
                <p class="mb-0 text-muted">
                    <span class="text-success mr-2"><span class="mdi mdi-arrow-up-bold"></span> 5.27%</span>
                    <span class="text-nowrap">Since last month</span>  
                </p>
            </div> <!-- end card-body-->
        </div>
        <!--end card-->

        <div class="card tilebox-one d-none">
            <div class="card-body">
                <i class='uil uil-window-restore float-right'></i>
                <h6 class="text-uppercase mt-0">Views per minute</h6>
                <h2 class="my-2" id="active-views-count">560</h2>
                <p class="mb-0 text-muted">
                    <span class="text-danger mr-2"><span class="mdi mdi-arrow-down-bold"></span> 1.08%</span>
                    <span class="text-nowrap">Since previous week</span>
                </p>
            </div> <!-- end card-body-->
        </div>
        <!--end card-->

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

    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-body">
                <ul class="nav float-right d-none">
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">7d</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">15d</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">1m</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-muted" href="#">1y</a>
                    </li>
                </ul>
                <h4 class="header-title mb-3">Sales Overview</h4>

                <div id="sessions-overview" class="apex-charts mt-3" data-colors="#0acf97"></div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div>
</div>






<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <h4 class="header-title mb-3">Products with negative stock</h4>
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

    /**
     * Monthly Sales Totals
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
        select_period = $(" #select_period ").val();
        year_id = $(" #year_id ").val();

        var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        var quarters = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];

        $(" #table-sales-totals-thead ").empty();
        $(" #table-sales-totals-tbody ").empty();

        $(" #table-sales-totals-thead ").append('<th>Category</th>');

        if (select_period=='period_monthly')
        {
            $.each(months, function( index, value ) {
                $(" #table-sales-totals-thead ").append('<th>'+value+'</th>');
            });

        }
        else if (select_period=='period_quarterly')
        {
            $.each(quarters, function( index, value ) {
                $(" #table-sales-totals-thead ").append('<th>'+value+'</th>');
            });

        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var route = '{{ route("ajax.sales-totals") }}';

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'json',
            data: { 
                'period': select_period,
                'year': year_id,
                'month': '_ALL',
                'quarter': '_ALL',
                'category': '',
                '_method': 'GET'
            },
            success : function(result)
            {
                console.log(result);
                
                $.each(result, function(index, category) {
                    $(" #table-sales-totals-tbody ").append('<tr>');

                    $(" #table-sales-totals-tbody ").append('<td>'+index+'</td>');

                    $.each(category, function(key, month){
                        $(" #table-sales-totals-tbody ").append('<td>'+month.total_amount+'</td>');
                    });

                    $(" #table-sales-totals-tbody ").append('</tr>');
                });
            }
        });
    }

    /**
     *  datatables for negative stock
     * 
     */
    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });

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



    function getRandomData(number) {
        var graphData = [];
        for (var idx=0; idx < number; idx++) {
            graphData.push(Math.floor(Math.random() * Math.floor(90)) + 30);
        }
        return graphData;
    }

    function getDaysInMonth(month, year) {
        var date = new Date(year, month, 1);
        var days = [];
        var idx = 0;
        while (date.getMonth() === month && idx < 15) {
            var d = new Date(date);
            days.push(d.getDate() + " " +  d.toLocaleString('en-us', { month: 'short' }));
            date.setDate(date.getDate() + 1);
            idx += 1;
        }
        return days;
    }

    // Sales overview
    var now = new Date();
    var labels = getDaysInMonth(now.getMonth() + 1, now.getFullYear());
       var options = {
            chart: {
                height: 400,
                type: 'area'
            },
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center', 
                floating: false,
                fontSize: '14px',
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            series: [],
            noData: {
                text: 'Loading...'
            },
            zoom: {
                enabled: true
            },
            colors: ["#e57373", "#7986cb", "#4db6ac", "#fff176", "#f06292", "#64b5f6", "#81c784", "#ffb74d", "#ba68c8", "#4dd0e1"],
            xaxis: {
                type: 'string',
                categories: labels,
                tooltip: {
                    enabled: false
                },
                axisBorder: {
                    show: false
                },
                labels: {
                    
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return val + "k"
                    },
                    offsetX: -15
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    type: "vertical",
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [45, 100]
                  },
            },
        }

        var chart = new ApexCharts(
            document.querySelector("#sessions-overview"),
            options
        );

        chart.render();

        var url = "{{ route('sale-orders-items.sales-by-category', ['range' => 'monthly']) }}";

        $.getJSON(url, function(response) {
            $.each(response.series, function(k, v) {
                chart.updateSeries([{
                    name: v.name,
                    data: v.data
                }]) 
            });
        });

});

    </script>    
@endsection

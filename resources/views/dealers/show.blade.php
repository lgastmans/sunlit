@extends('layouts.app')

@section('title')
    @parent() | Dealer {{ $dealer->company }}
@endsection

@section('page-title')
    Dealer {{ $dealer->company }}
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8">
        <!-- Profile -->
        <div class="card bg-secondary">
            <div class="card-body profile-user-box">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    <!-- <img src="/images/users/avatar-2.jpg" alt="" class="rounded-circle img-thumbnail"> -->
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <h4 class="mt-1 mb-1 text-white">{{ $dealer->contact_person }}</h4>
                                    <p class="font-13 text-white-50"> {{ $dealer->company }}</p>

                                    <ul class="mb-0 list-inline text-light d-none">
                                        <li class="list-inline-item me-3">
                                            <h5 class="mb-1">$ 25,184</h5>
                                            <p class="mb-0 font-13 text-white-50">Total Sales</p>
                                        </li>
                                        <li class="list-inline-item d-none">
                                            <h5 class="mb-1">5482</h5>
                                            <p class="mb-0 font-13 text-white-50">Number of Orders</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col-->

                    <div class="col-sm-4">
                        <div class="text-center mt-sm-0 mt-3 text-sm-end">
                            @can('edit dealers')
                            <a href="{{ route('dealers.edit', $dealer->id) }}">
                            <button type="button" class="btn btn-light">
                                <i class="mdi mdi-account-edit me-1"></i> Edit dealer
                            </button>
                        </a>
                            @endcan
                        </div>
                    </div> <!-- end col-->
                    
                </div> <!-- end row -->

            </div> <!-- end card-body/ profile-user-box-->
        </div><!--end profile/ card -->

        <!-- Toll free number box-->
        <div class="card col-sm-4 text-white bg-info overflow-hidden">
            <div class="card-body">
                <div class="toll-free-box text-center">
                    <h4> <i class="mdi mdi-deskphone"></i> {{ $dealer->phone }}</h4>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
        <!-- End Toll free number box-->
    </div> <!-- end col-->

    <div class="col-xl-4">
        <!-- Personal-Information -->
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mt-0 mb-3">Dealer Information</h4>
                {{-- <p class="text-muted font-13">
                    Hye, I’m Michael Franklin residing in this beautiful world. I create websites and mobile apps with great UX and UI design. I have done work with big companies like Nokia, Google and Yahoo. Meet me or Contact me for any queries. One Extra line for filling space. Fill as many you want.
                </p> --}}

                <hr/>

                <div class="text-start">
                    <p class="text-muted"><strong>Full Name :</strong> <span class="ms-2">{{ $dealer->contact_person }}</span></p>

                    <p class="text-muted"><strong>Phone :</strong><span class="ms-2">{{ $dealer->phone }} / {{ $dealer->phone2 }}</span></p>

                    <p class="text-muted"><strong>Email :</strong> <span class="ms-2">{{ $dealer->email }}</span></p>

                    <p class="text-muted"><strong>Location :</strong> <span class="ms-2">{{ $dealer->address }}, {{ $dealer->city }}, {{ $dealer->state->name }} {{ $dealer->zip_code }}</span></p>

                    @if ($dealer->gstin)
                    <p class="text-muted"><strong>GSTIN :</strong><span class="ms-2">{{ $dealer->gstin }}</span></p>
                    @endif


                </div>
            </div>
        </div>
        <!-- Personal-Information -->


       

    </div> <!-- end col-->




</div>
<!-- end row -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-secondary text-light">
                <h4>Sales Report</h4>
            </div>
            <div class="card-body">
                <div class="mb-3 row">

                    <div class="col-xl-4">
                        <label class="form-label" for="dealer-select">Dealer</label>
                        <input type="hidden" name="dealer_id" id="select_dealer" value="{{$dealer->id}}">
                        {{-- <select class="dealer-select form-control" name="dealer_id" id="select_dealer">
                            <option value="" selected="selected"></option>
                        </select> --}}
                        <div class="invalid-feedback">
                            {{ __('error.form_invalid_field', ['field' => 'dealer' ]) }}
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <label class="form-label" for="format-select">Format</label>
                        <select class="format-select form-control" name="format_id" id="select_format">
                            <option value="format_datewise" selected>Date - Invoice No</option>
                            <option value="format_category">Category</option>
                        </select>
                    </div>

                    <div class="col-xl-1">
                        <label class="form-label" for="period-select">Period</label>
                        <select class="period-select form-control" name="period_id" id="select_period">
                            <option value="period_monthly" selected>Monthly</option>
                            <option value="period_quarterly">Quarterly</option>
                            <!-- <option value="period_yearly">Yearly</option> -->
                        </select>
                    </div>

                    
                    {{-- Monthly - month --}}

                    <div class="col-xl-2" id="display_monthly_month">
                        <label class="form-label">&nbsp;</label>
                        <select class="month-select form-control" id="month_id">
                            @foreach(range(1,12) as $month)
                                    <option value="{{$month}}" {{ $month == date('m') ? "selected" : "" }} >{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Quarterly --}}

                    <div class="col-xl-2" id="display_period_quarterly" style="display:none">
                        <label class="form-label">&nbsp;</label>
                        <select class="quaterly-select form-control" id="quarterly_id">
                            <option value="Q1">January 1 – March 31</option>
                            <option value="Q2">April 1 – June 30</option>
                            <option value="Q3">July 1 – September 30</option>
                            <option value="Q4">October 1 – December 31</option>
                        </select>
                    </div>


                    {{-- Monthly - year --}}

                    <div class="col-xl-1 position-relative " id="display_monthly_year">
                        <label class="form-label">&nbsp;</label>
                        <input type="text" class="form-control " id="year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                        data-date-container="#display_monthly_year">
                    </div>

                    <div class="col-xl-1">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-primary form-control" type="button" id="btn-load">Load</button>
                    </div>

                </div>   
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table id="table-report-datewise" class="table table-striped table-condensed " cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice Number</th>
                        <th>Part Number</th>
                        <th>Model</th>
                        <th>KW Rating</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Taxable Value</th>
                        <th>Tax</th>
                        <th>Tax Amount</th>
                        <th>Amount</th>
                    </tr>
                    <tr class="filters-datewise" style="display: none;">
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th><input type="text" class="form-control"></th>
                        <th><input type="text" class="form-control"></th>
                        <th><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
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
                    </tr>
                </tfoot>
            </table>

            <table id="table-report-category" class="table table-striped table-condensed" cellspacing="0" width="100%" style="display:none">
            <thead>
                    <tr>
                        <th>Category</th>
                        <th>Part Number</th>
                        <th>Model</th>
                        <th>KW Rating</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Taxable Value</th>
                        <th>Tax</th>
                        <th>Tax Amount</th>
                        <th>Amount</th>
                    </tr>
                    <tr class="filters-category" style="display: none;">
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th><input type="text" class="form-control"></th>
                        <th><input type="text" class="form-control"></th>
                        <th><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
                        <th class="no-filter"><input type="text" class="form-control"></th>
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
                    </tr>
                </tfoot>

            </table>

        </div> {{-- table-responsive --}}
    </div>
</div>

@endsection
@section('page-scripts')
<script>

    $(document).ready(function () {
        "use strict";

        $('#year_id').datepicker({
            format: 'yyyy',
            minViewMode: 2,
            maxViewMode: 2,
            autoclose: true,
        });

        // var dealerSelect = $(".dealer-select").select2();
        // dealerSelect.select2({
        //     ajax: {
        //         url: '{{route('ajax.dealers')}}',
        //         dataType: 'json'
        //     }
        // });


        $(" #select_format ").on("change", function() {
            if ($(this).val()=='format_datewise')
            {
                $(" #table-report-datewise ").show();
                $(" #table-report-category ").hide();
                $(" #table-report-datewise_wrapper ").show();
                $(" #table-report-category_wrapper ").hide();
            }
            else if ($(this).val()=='format_category')
            {
                $(" #table-report-datewise ").hide();
                $(" #table-report-category ").show();
                $(" #table-report-datewise_wrapper ").hide();
                $(" #table-report-category_wrapper ").show();
            }
        })

        $(" #select_period ").on("change", function() {

            if ($(this).val()=='period_monthly')
            {
                $(" #display_monthly_month ").show();
                $(" #display_monthly_year ").show();
                $(" #display_period_quarterly ").hide();
            }
            else if ($(this).val()=='period_quarterly')
            {
                $(" #display_monthly_month ").hide();
                $(" #display_monthly_year ").show();
                $(" #display_period_quarterly ").show();
            }
            else if ($(this).val()=='period_yearly')
            {
                $(" #display_monthly_month ").hide();
                $(" #display_monthly_year ").hide();
                $(" #display_period_quarterly ").hide();
            }
        });


        var dealer_id,
            select_format,
            select_period,
            month_id,
            year_id,
            quarterly_id,
            footerData;

        var reportTable = $(" #table-report-datewise ").DataTable({
            processing      : true,
            serverSide      : true,
            deferLoading    : 0,
            //"scrollY" 		: '80vh',
            scrollCollapse  : true,
            paging		    : false,				        
            searching	    : true,
            filter          : true,
            info            : false,
            autoWidth       : false,
            dom: 'Blrtip', //'Bfrtip',
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
                    text: '<i class="mdi mdi-filter"></i>&nbsp;Filter',
                    // className: 'btn btn-light',
                    action: function ( e, dt, node, config ) {
                        $( ".filters-datewise" ).slideToggle('slow');
                    }
                }
            ],
            ajax            : 
            {
                method 	: "GET",
				url 	: "{{ route('ajax.sales-report') }}",
                dataSrc : "data.data",
                data  : function ( d ) {
                    d.dealer_id = dealer_id,
                    d.select_format = select_format,
                    d.select_period = select_period,
                    d.month_id = month_id,
                    d.year_id = year_id,
                    d.quarterly_id = quarterly_id
                }
            },            
            columns   : [
                { data: 'invoice_date', orderable : false},
                { data: 'invoice_number', orderable : false},
                { data: 'part_number', orderable : false},
                { data: 'model', orderable : false},
                { data: 'kw_rating', orderable : false},
                { data: 'quantity', orderable : false},
                { data: 'price', orderable : false},
                { data: 'taxable_value', orderable : false},
                { data: 'tax', orderable : false},
                { data: 'tax_amount', orderable : false},
                { data: 'amount', orderable : false}
            ],
            "columnDefs": [
                { className: "dt-right", "targets": [5,6,7,8,9,10] },  //'_all' }
            ],
            oLanguage : {
                "sInfo": "", //"_TOTAL_ entries",
                "sInfoEmpty": "No entries",
                "sEmptyTable": "No data to display",
            },

            fnInitComplete : function () {
                // Event handler to be fired when rendering is complete (Turn off Loading gif for example)
                // console.log('Datatable rendering complete');
            },



            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                var json = api.ajax.json();

                if (json!==undefined)
                {
   
                    $( api.column( 0 ).footer() ).html(json.data.footer.label );
                    $( api.column( 5 ).footer() ).html(json.data.footer.total_quantity );
                    $( api.column( 7 ).footer() ).html(json.data.footer.total_taxable_value );
                    $( api.column( 9 ).footer() ).html(json.data.footer.total_tax_amount );
                    $( api.column( 10 ).footer() ).html(json.data.footer.total_amount );
                }
            }
        });

        reportTable.columns().eq(0).each(function(colIdx) {

            var cell = $('.filters-datewise th').eq($(reportTable.column(colIdx).header()).index());
            var title = $(cell).text();

            if($(cell).hasClass('no-filter')){
                $(cell).html('&nbsp');
            }
            else{
                $('input', $('.filters-datewise th').eq($(reportTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                    e.stopPropagation();
                    $(this).attr('title', $(this).val());
                    reportTable
                        .column(colIdx)
                        .search(this.value)
                        .draw();
                    
                }); 
            }
            });

        var reportTableCategory = $(" #table-report-category ").DataTable({
            processing      : true,
            serverSide      : true,
            deferLoading    : 0,
            scrollCollapse  : true,
            paging		    : false,				        
            searching	    : true,
            filter          : true,
            info            : false,
            autoWidth       : false,
            dom: 'Blrtip', //'Bfrtip',
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
                    text: '<i class="mdi mdi-filter"></i>&nbsp;Filter',
                    // className: 'btn btn-light',
                    action: function ( e, dt, node, config ) {
                        $( ".filters-category" ).slideToggle('slow');
                    }
                }
            ],
            ajax            : 
            {
                method 	: "GET",
				url 	: "{{ route('ajax.sales-report') }}",
                dataSrc : "data.data",
                data  : function ( d ) {
                    d.dealer_id = dealer_id,
                    d.select_format = select_format,
                    d.select_period = select_period,
                    d.month_id = month_id,
                    d.year_id = year_id,
                    d.quarterly_id = quarterly_id
                }
            },            
            columns   : [
                { data: 'category', orderable : false},
                { data: 'part_number', orderable : false, 'width': '100px'},
                { data: 'model', orderable : false},
                { data: 'kw_rating', orderable : false},
                { data: 'quantity', orderable : false},
                { data: 'price', orderable : false},
                { data: 'taxable_value', orderable : false},
                { data: 'tax', orderable : false},
                { data: 'tax_amount', orderable : false},
                { data: 'amount', orderable : false}
            ],
            columnDefs: [
                { className: "dt-right", "targets": [3,4,5,6,7,8,9] },   //'_all' }
                { visible: false, "targets": [0] },
            ],
            oLanguage : {
                "sInfo": "", //"_TOTAL_ entries",
                "sInfoEmpty": "No entries",
                "sEmptyTable": "No data to display",
            },
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows().nodes();
                var last = null;
    
                api
                    .column(0)  // column 0 for category
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            $(rows)
                                .eq(i)
                                .before('<tr style="background-color:green"><td colspan="9"><b>' + group + '</b></td></tr>');
    
                            last = group;
                        }
                    });
            },            
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                var json = api.ajax.json();

                if (json!==undefined)
                {
                    $( api.column( 0 ).footer() ).html(json.data.footer.label );
                    $( api.column( 4 ).footer() ).html(json.data.footer.total_quantity );
                    $( api.column( 6 ).footer() ).html(json.data.footer.total_taxable_value );
                    $( api.column( 8 ).footer() ).html(json.data.footer.total_tax_amount );
                    $( api.column( 9 ).footer() ).html(json.data.footer.total_amount );
                }
            }

        });

        reportTableCategory.columns().eq(0).each(function(colIdx) {

            var cell = $('.filters-category th').eq($(reportTableCategory.column(colIdx).header()).index());
            var title = $(cell).text();

            if($(cell).hasClass('no-filter')){
                $(cell).html('&nbsp');
            }
            else{
                $('input', $('.filters-category th').eq($(reportTableCategory.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                    e.stopPropagation();
                    $(this).attr('title', $(this).val());
                    reportTableCategory
                        .column(colIdx)
                        .search(this.value)
                        .draw();
                    
                }); 
            }
        });



        $(" #btn-load ").on("click", function() {

            dealer_id = $(" #select_dealer ").val();
            select_format = $(" #select_format ").val();
            select_period = $(" #select_period ").val();
            month_id = $(" #month_id ").val();
            year_id = $(" #year_id ").val();
            quarterly_id = $(" #quarterly_id ").val();

            if (select_format=='format_datewise')
            {
                reportTable.ajax.reload();
            }
            else
            {
                reportTableCategory.ajax.reload();
            }

        });

        setTimeout(function() {
            $(" #table-report-category_wrapper ").hide();
        }, 50);        

    });
    
</script>    

@endsection


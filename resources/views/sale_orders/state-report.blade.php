@extends('layouts.app')

@section('title')
    @parent() | State-Wise Sales Report
@endsection

@section('page-title', 'State-Wise Sales Report')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3 row">

                        <div class="col-xl-4">
                            <label class="form-label" for="state-select">State</label>
                            <select class="state-select form-control" name="state_id" id="select_state">
                                <option value="" selected="selected"></option>
                            </select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'state' ]) }}
                            </div>
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

                        <div class="col-xl-3">
                        </div>

                    </div>   
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="table-report-states" class="table table-striped table-condensed" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>State</th>
                            <th>Category</th>
                            <th>Part Number</th>
                            <th>Model</th>
                            <th>KW Rating</th>
                            <th style="text-align: right;">Quantity</th>
                            <th style="text-align: right;">Price</th>
                            <!-- <th>Taxable Value</th> -->
                            <!-- <th>Tax</th> -->
                            <!-- <th>Tax Amount</th> -->
                            <th style="text-align: right;">Amount</th>
                        </tr>
                        <tr class="filters-category" style="display: none;">
                            <th class="no-filter"><input type="text" class="form-control"></th>
                            <th class="no-filter"><input type="text" class="form-control"></th>
                            <th><input type="text" class="form-control"></th>
                            <th><input type="text" class="form-control"></th>
                            <th><input type="text" class="form-control"></th>
                            <th class="no-filter"><input type="text" class="form-control"></th>
                            <th class="no-filter"><input type="text" class="form-control"></th>
                            <!-- <th class="no-filter"><input type="text" class="form-control"></th> -->
                            <!-- <th class="no-filter"><input type="text" class="form-control"></th> -->
                            <!-- <th class="no-filter"><input type="text" class="form-control"></th> -->
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
                        </tr>
                    </tfoot>

                </table>

            </div> {{-- table-responsive --}}
        </div>
    </div>
@endsection <!-- content -->

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


        var stateSelect = $(".state-select").select2();
        stateSelect.select2({
            ajax: {
                url: '{{route('ajax.states')}}',
                dataType: 'json'
            }
        });


        $(" #select_period ").on("change", function() {
            //console.log('changed the period ' + $(this).val() );

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

        // $('#year_id').datetimepicker({
        //     format: 'YYYY',
        //     viewMode: "years",
        // });
        // $("#year_id").on("dp.hide", function (e) {
        //     $('#year').datetimepicker('destroy');
        //     $('#year').datetimepicker({
        //         format: 'YYYY',
        //         viewMode: "years",
        //     });
        // });

        var state_id,
            select_period,
            month_id,
            year_id,
            quarterly_id,
            footerData;


        var reportTableStates = $(" #table-report-states ").DataTable({
            processing      : true,
            serverSide      : true,
            deferLoading    : 0,
            scrollCollapse  : true,
            paging          : false,                        
            searching       : true,
            filter          : true,
            info            : false,
            autoWidth       : false,
            dom: 'Blrtip', //'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5 ]
                    },
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [ 0, 1, 2, 3, 4, 5 ]
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
                method  : "GET",
                url     : "{{ route('ajax.sales-state-report') }}",
                dataSrc : "data.data",
                data  : function ( d ) {
                    d.state_id = state_id,
                    d.select_period = select_period,
                    d.month_id = month_id,
                    d.year_id = year_id,
                    d.quarterly_id = quarterly_id
                }
            },            
            columns   : [
                { data: 'state', orderable : false},
                { data: 'category', orderable : false},
                { data: 'part_number', orderable : false, 'width': '100px'},
                { data: 'model', orderable : false},
                { data: 'kw_rating', orderable : false},
                { data: 'quantity', orderable : false},
                { data: 'price', orderable : false},
                // { data: 'taxable_value', orderable : false},
                // { data: 'tax', orderable : false},
                // { data: 'tax_amount', orderable : false},
                { data: 'amount', orderable : false}
            ],
            columnDefs: [
                { className: "dt-right", "targets": [3,4,5,6,7] },   //'_all' }
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
                    .column(0)  // column 0 for state
                    .data()
                    .each(function (group, i) {
                        if (last !== group) {
                            
                            $(rows)
                                .eq(i)
                                .before('<tr class="datatables-report-group-header"><td colspan="10"><b>' + group + '</b></td></tr>');
                                
                            last = group;
                        }
                        else{
                            $(rows).removeClass('text-danger');
                        }
                    });
            },            
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                var json = api.ajax.json();

                if (json!==undefined)
                {
                    $( api.column( 0 ).footer() ).html(json.data.footer.label );
                    $( api.column( 5 ).footer() ).html(json.data.footer.total_quantity );
                    // $( api.column( 7 ).footer() ).html(json.data.footer.total_taxable_value );
                    // $( api.column( 9 ).footer() ).html(json.data.footer.total_tax_amount );
                    $( api.column( 7 ).footer() ).html(json.data.footer.total_amount );
                }
            }

        });

        reportTableStates.columns().eq(0).each(function(colIdx) {

            var cell = $('.filters-category th').eq($(reportTableStates.column(colIdx).header()).index());
            var title = $(cell).text();

            if($(cell).hasClass('no-filter')){
                $(cell).html('&nbsp');
            }
            else{
                $('input', $('.filters-category th').eq($(reportTableStates.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                    e.stopPropagation();
                    $(this).attr('title', $(this).val());
                    reportTableStates
                        .column(colIdx)
                        .search(this.value)
                        .draw();
                    
                }); 
            }
        });



        $(" #btn-load ").on("click", function() {

            state_id = $(" #select_state ").val();
            select_period = $(" #select_period ").val();
            month_id = $(" #month_id ").val();
            year_id = $(" #year_id ").val();
            quarterly_id = $(" #quarterly_id ").val();

            reportTableStates.ajax.reload();
            reportTableStates.columns.adjust();

        });        

    });
    
</script>    

@endsection <!-- page-scripts -->

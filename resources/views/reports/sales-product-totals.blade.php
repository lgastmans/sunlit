@extends('layouts.app')

@section('title')
    @if ($report_format=='sales')
        @parent() | Product Sales Total
    @else
        @parent() | Product Quantity Total
    @endif
@endsection

@if ($report_format=='sales')
    @section('page-title', 'Product Sales Total')
@else
    @section('page-title', 'Product Quantity Total')
@endif

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3 row">

                        <div class="col-xl-1">
                            <label class="form-label" for="period-select">Period</label>
                            <select class="period-select form-control" name="period_id" id="select_period">
                                <option value="period_monthly" selected>Monthly</option>
                                <option value="period_quarterly">Quarterly</option>
                                <option value="period_yearly">Yearly</option>
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
                                <option value="Q1" @if ($curQuarter=='Q1') selected @endif>January 1 – March 31</option>
                                <option value="Q2" @if ($curQuarter=='Q2') selected @endif>April 1 – June 30</option>
                                <option value="Q3" @if ($curQuarter=='Q3') selected @endif>July 1 – September 30</option>
                                <option value="Q4" @if ($curQuarter=='Q4') selected @endif>October 1 – December 31</option>
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
                            <button class="btn btn-primary form-control" type="button" id="btn-load">
                                <!-- <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" id="spinner"></span> -->
                                Load
                            </button>
                        </div>

                        <div class="col-xl-4">
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
<!--                 
                <table id="table-product-sales" class="table table-striped table-condensed" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Id</th>
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

 -->
                <table id="table-product-sales" class="table table-striped table-condensed" cellspacing="0" width="100%">
                </table>

            </div> {{-- table-responsive --}}
        </div>
    </div>


@endsection <!-- content -->

@section('page-scripts')

<script>

    $(document).ready(function () {
        "use strict";    

        //$(" #spinner ").hide();
        //$(this).html('Load');

        var select_period,
            month_id,
            year_id,
            quarterly_id,
            footerData;

        var table,
            data,
            tableName = '#table-product-sales';


        $('#year_id').datepicker({
            format: 'yyyy',
            minViewMode: 2,
            maxViewMode: 2,
            autoclose: true,
        })
        // .on("change", function() {
        //     productSalesTable.ajax.reload();
        // });


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
                $(" #display_period_quarterly ").hide();
            }
            else if ($(this).val()=='period_yearly')
            {
                $(" #display_monthly_month ").hide();
                $(" #display_monthly_year ").show();
                $(" #display_period_quarterly ").hide();
            }
        });


        $(" #btn-load ").on("click", function() {

            //$(" #spinner ").show();
            $(this).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" id="spinner"></span>Loading...');

            select_period = $(" #select_period ").val();
            month_id = $(" #month_id ").val();
            year_id = $(" #year_id ").val();
            quarterly_id = "_ALL"; //$(" #quarterly_id ").val();

            $.ajax({
                method  : "GET",
                url     : "{{ route('ajax.sales-product-totals') }}",
                data    : { 
                    report_format : '{{ $report_format }}',
                    select_period : select_period,
                    month_id : month_id,
                    year_id : year_id,
                    quarterly_id : quarterly_id
                }                
            })
            .done ( function( msg ) {

                //$(" #spinner ").hide();
                $("#btn-load").html('Load');

                data = JSON.parse(msg);
                console.log(data);
        
                if (table) {
                    table.clear();
                    table.destroy();
                    // clear the column headers
                    $(tableName).html(''); 
                }

                table = $(tableName).DataTable({
                    dom: 't',
                    // processing: true,
                    serverSide: true,
                    orderCellsTop: true,
                    retrieve: true,
                    fixedHeader: true,
                    deferLoading: 0,
                    searching: true,
                    paging: false,
                    ordering: false,
                    data          : data.data,
                    columns       : data.columns,
                    footer        : data.footer,            
                    // "language": {
                    //     "paginate": {
                    //         "previous": "<i class='mdi mdi-chevron-left'>",
                    //         "next": "<i class='mdi mdi-chevron-right'>"
                    //     },
                    //     "info": "Showing _START_ to _END_ of _TOTAL_",
                    //     "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                    //         '<option value="10">10</option>' +
                    //         '<option value="20">20</option>' +
                    //         '<option value="-1">All</option>' +
                    //         '</select> rows'
                    // },
                    // columnDefs: [
                    //     { className: "dt-right", "targets": [2,3,4,5,6,7,8,9,10,11,12,13,14] },   //'_all' }
                    // ],            
                });

                //table.ajax.reload();
            });
        });


    }); // document ready

</script>    

@endsection <!-- page-scripts -->

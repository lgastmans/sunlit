@extends('layouts.app')

@section('title')
    @parent() | Dealer Ledger
@endsection

@section('page-title', 'Dealer Total Sales Summary')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3 row">

                        <div class="col-xl-3">
                        </div>

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
                            <button class="btn btn-primary form-control" type="button" id="btn-load">Load</button>
                        </div>

                        <div class="col-xl-1">
                        </div>

                        <div class="col-xl-1">
                        </div>

                    </div>   
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>


    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="table-ledger-summary" class="table table-striped table-condensed " cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Client</th>
                            <th>Debit</th>
<!--                             <th>Credit</th>
                            <th>Balance</th>
 -->                        
                        </tr>
                        <tr class="filters-category">
                            <th class="no-filter"></th>
                            <th><input type="text" class="form-control" value=""></th>
                            <th class="no-filter"></th>
                        </tr>                        
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
<!--                             <th></th>
                            <th></th>
 -->                        
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

        var select_period = $(" #select_period ").val();
        var month_id = $(" #month_id ").val();
        var year_id = $(" #year_id ").val();
        var quarterly_id = $(" #quarterly_id ").val();       

        $('#year_id').datepicker({
            format: 'yyyy',
            minViewMode: 2,
            maxViewMode: 2,
            autoclose: true,
        });

        var ledgerTable = $(" #table-ledger-summary ").DataTable({
            processing      : true,
            serverSide      : true,
            //deferLoading    : 0,
            //"scrollY"         : '80vh',
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
                // {
                //     text: '<i class="mdi mdi-filter"></i>&nbsp;Filter',
                //     // className: 'btn btn-light',
                //     action: function ( e, dt, node, config ) {
                //         $( ".filters-datewise" ).slideToggle('slow');
                //     }
                // }
            ],
            ajax            : 
            {
                method  : "GET",
                url     : "{{ route('ajax.dealers-ledger-summary') }}",
                dataSrc : "data.data",
                data  : function ( d ) {
                    d.select_period = select_period,
                    d.month_id = month_id,
                    d.year_id = year_id,
                    d.quarterly_id = quarterly_id
                },
            },            
            columns   : [
                { data: 'serial_number', orderable : false, visible: false},
                { 
                    data: 'dealer', 
                    orderable : false,
                    "render": function(data, type, row, meta){
                        data = '<a href="/dealers/ledger?id=' + row.dealer_id + '" target="_blank">' + data + '</a>';
                        return data;
                    }
                },
                { data: 'debit', orderable : true},
                { data: 'credit', orderable : false, visible: false},
                { data: 'balance', orderable : false, visible: false}
            ],
            "columnDefs": [
                { className: "dt-right", "targets": [2,3,4] },  //'_all' }
            ],
            "aaSorting": [[2, "desc"]],            
            oLanguage : {
                "sInfo": "", //"_TOTAL_ entries",
                "sInfoEmpty": "No entries",
                "sEmptyTable": "No data to display",
            },

            fnInitComplete : function () {
                // Event handler to be fired when rendering is complete (Turn off Loading gif for example)
                // console.log('Datatable rendering complete');
            },

            // fnRowCallback : function (nRow, aData, iDisplayIndex) {
            //     if (aData.hsn=='TOTALS')
            //         $(nRow).css("font-weight", "bold");
            // },

            // drawCallback: function(response) {
            //     var api = this.api();
            //     var json = api.ajax.json();

            //     if (json!==undefined)
            //     {
            //         console.log('object ' + json.data.footer);
            //         $( api.column( 0 ).footer() ).html(json.data.footer.label );
            //         $( api.column( 3 ).footer() ).html(json.data.footer.total_quantity );
            //         $( api.column( 6 ).footer() ).html(json.data.footer.total_amount );
            //     }
            // },

            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api();
                var json = api.ajax.json();

                if (json!==undefined)
                {
                    // console.log('object ' + json.data.footer.label);
                    $( api.column( 1 ).footer() ).html(json.data.footer.label );
                    $( api.column( 2 ).footer() ).html(json.data.footer.debit_total );
                    $( api.column( 3 ).footer() ).html(json.data.footer.credit_total );
                    $( api.column( 4 ).footer() ).html(json.data.footer.balance_total );
                }
            }
        });

        ledgerTable.columns().eq(0).each(function(colIdx) {

            var cell = $('.filters-datewise th').eq($(ledgerTable.column(colIdx).header()).index());
            var title = $(cell).text();

            if($(cell).hasClass('no-filter')){
                $(cell).html('&nbsp');
            }
            else{
                $('input', $('.filters-category th').eq($(ledgerTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                    e.stopPropagation();
                    $(this).attr('title', $(this).val());
                    ledgerTable
                        .column(colIdx)
                        .search(this.value)
                        .draw();
                    
                }); 
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
                $(" #display_monthly_year ").show();
                $(" #display_period_quarterly ").hide();
            }
        });

        $(" #btn-load ").on("click", function() {

            select_period = $(" #select_period ").val();
            month_id = $(" #month_id ").val();
            year_id = $(" #year_id ").val();
            quarterly_id = $(" #quarterly_id ").val();

            ledgerTable.ajax.reload();

        }); 

    });
    
</script>    

@endsection <!-- page-scripts -->

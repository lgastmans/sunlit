@extends('layouts.app')

@section('title')
    @parent() | Dealer Ledger
@endsection

@section('page-title', 'Dealer Ledger')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3 row">

                        <div class="col-xl-4">
                            <label class="form-label" for="dealer-select">Dealer</label>
                            <select class="dealer-select form-control" name="dealer_id" id="select_dealer">
                                <option value="" selected="selected"></option>
                            </select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'dealer' ]) }}
                            </div>
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
                <table id="table-ledger" class="table table-striped table-condensed " cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Vch Type</th>
                            <th>Vch No</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th>Totals</th>
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

        var dealerSelect = $(".dealer-select").select2();
        dealerSelect.select2({
            ajax: {
                url: '{{route('ajax.dealers')}}',
                dataType: 'json'
            }
        });

        var dealer_id,
            footerData;

        var ledgerTable = $(" #table-ledger ").DataTable({
            processing      : true,
            serverSide      : true,
            deferLoading    : 0,
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
                method  : "GET",
                url     : "{{ route('ajax.dealers-ledger') }}",
                dataSrc : "data.data",
                data  : function ( d ) {
                    d.dealer_id = dealer_id
                }
            },            
            columns   : [
                { data: 'vch_date', orderable : false},
                { data: 'particulars', orderable : false},
                { data: 'vch_type', orderable : false},
                { data: 'vch_no', orderable : false},
                { data: 'debit', orderable : false},
                { data: 'credit', orderable : false}
            ],
            "columnDefs": [
                { className: "dt-right", "targets": [4,5] },  //'_all' }
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
                    //$( api.column( 0 ).footer() ).html(json.data.footer.label );
                    //$( api.column( 5 ).footer() ).html(json.data.footer.total_quantity );
                    $( api.column( 4 ).footer() ).html(json.data.footer.debit_total );
                    $( api.column( 5 ).footer() ).html(json.data.footer.credit_total );
                    //$( api.column( 10 ).footer() ).html(json.data.footer.total_amount );
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
                $('input', $('.filters-datewise th').eq($(ledgerTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                    e.stopPropagation();
                    $(this).attr('title', $(this).val());
                    ledgerTable
                        .column(colIdx)
                        .search(this.value)
                        .draw();
                    
                }); 
            }
        });

        $(" #btn-load ").on("click", function() {

            dealer_id = $(" #select_dealer ").val();

            ledgerTable.ajax.reload();

        });

    });
    
</script>    

@endsection <!-- page-scripts -->

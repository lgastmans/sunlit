@extends('layouts.app')

@section('title')
    @parent() | Stock Report
@endsection

@section('page-title', 'Stock Report')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3 row">

                        <div class="col-xl-1">
                        </div>

                        <div class="col-xl-2" id="stock_at">
                            <label class="form-label">Stock as on</label>
                            <input type="text" class="form-control" name="closing_stock_at" id="closing_stock_at" value="{{ date('d-m-Y') }}"
                            data-provide="datepicker" 
                            data-date-container="#stock_at"
                            data-date-autoclose="true"
                            data-date-format="dd-mm-yyyy"
                            data-date-today-highlight="true"
                            required>
                        </div>

                        <div class="col-xl-1">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary form-control" type="button" id="btn-load">Load</button>
                        </div>

                        <div class="col-xl-8">
                        </div>


                    </div>   
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-bordered table-hover w-100 dt-responsive nowrap" id="stock-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Warehouse</th>
                                    <th>Part Number</th>
                                    <th>Description</th>
                                    <th>Stock</th>
                                </tr>
                                <tr class="filters"> 
                                    <th><input type="text" class="form-control"></th>
                                    <th><input type="text" class="form-control"></th>      
                                    <th class="no-filter"></th>
                                    <th class="no-filter"></th>
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

@endsection <!-- content -->

@section('page-scripts')

<script>

    $(document).ready(function () {
        "use strict";

        $('.toggle-filters').on('click', function(e) {
            $( ".filters" ).slideToggle('slow');
        });

    var select_period;

    var stockTable = $('#stock-datatable').DataTable({
        dom: 'Bfrtip',
        stateSave: true,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3]
                },
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3]
                },
                className: 'btn btn-warning',
                download: 'open'
            },
            // {
            //     extend: 'colvis',
            //     columns: ':not(.noVis)',
            //     className: 'btn btn-info'
            // },
            // {
            //     text: '<i class="mdi mdi-filter"></i>&nbsp;Filter',
            //     // className: 'btn btn-light',
            //     action: function ( e, dt, node, config ) {
            //         $( ".filters" ).slideToggle('slow');
            //     }
            // }
        ],
        processing: true,
        serverSide: true,
        orderCellsTop: true,
        fixedHeader: true,
        //deferLoading: 0,
        searching: true,
        paging: false,
        ajax      : {
            url   : "{{ route('ajax.inventory-closing-stock') }}",
            data  : function ( d ) {
                d.select_period = select_period
            }            
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
        "columns": [
            { 
                'data': 'warehouse',
                'orderable': true
            },
            { 
                'data': 'part_number',
                'orderable': true
            },
            { 
                'data': 'description',
                'orderable': true
            },
            { 
                'data': 'closing_stock',
                'orderable': false
            },
        ],
        
        "aaSorting": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#stock-datatable_length label').addClass('form-label');
            
        },
        
    });

    stockTable.columns().eq(0).each(function(colIdx) {

        var cell = $('.filters th').eq($(stockTable.column(colIdx).header()).index());
        var title = $(cell).text();

        if($(cell).hasClass('no-filter')){

            $(cell).html('&nbsp');

        }
        else{

            // $(cell).html( '<input class="form-control filter-input" type="text"/>' );

            $('select', $('.filters th').eq($(stockTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                //var regexr = '({search})'; //$(this).parents('th').find('select').val();
                stockTable
                    .column(colIdx)
                    .search(this.value) //(this.value != "") ? regexr.replace('{search}', 'this.value') : "", this.value != "", this.value == "")
                    .draw();
                 
            });
            
            $('input', $('.filters th').eq($(stockTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                //var regexr = '({search})'; //$(this).parents('th').find('select').val();
                stockTable
                    .column(colIdx)
                    .search(this.value) //(this.value != "") ? regexr.replace('{search}', 'this.value') : "", this.value != "", this.value == "")
                    .draw();
                 
            }); 
        }
    });
        
    $(" #btn-load ").on("click", function() {

        select_period = $(" #closing_stock_at ").val();
        //month_id = $(" #month_id ").val();
        //year_id = $(" #year_id ").val();
        //quarterly_id = $(" #quarterly_id ").val();
console.log('test ' + select_period);

        stockTable.ajax.reload();

    });

    });

</script>    

@endsection <!-- page-scripts -->

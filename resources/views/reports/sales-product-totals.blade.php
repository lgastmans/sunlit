@extends('layouts.app')

@section('title')
    @parent() | Product-wise Sales Total
@endsection

@section('page-title', 'Product-wise Sales Total')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="row justify-content-start">
                            <span class="col-1 pt-1">Year</span>
                            <div class="col-2" id="display_product_year">
                                <input type="text" class="form-control " id="product_year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                                data-date-container="#display_product_year">
                            </div>  
                        </div>
                    </div>   
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-12">
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


@endsection <!-- content -->

@section('page-scripts')

<script>

    $(document).ready(function () {
        "use strict";    

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

    }); // document ready

</script>    

@endsection <!-- page-scripts -->

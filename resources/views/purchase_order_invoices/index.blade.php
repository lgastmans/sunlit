@extends('layouts.app')

@section('title')
    @parent() | Invoices
@endsection

@section('page-title', 'Purchase Orders Invoices')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
               
                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap table-has-dlb-click" id="purchase-order-invoices-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Order #</th>
                                <th>Shipped On</th> 
                                <th>Amount</th> 
                                <th style="width:100px;">Status</th> 
                                <th>Created By</th> 
                            </tr>
                            <tr class="filters" style="display: none;" >
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th id="shipped_at" class="position-relative">
                                    <input type="text" class="form-control" name="shipped_at" 
                                    data-provide="datepicker" 
                                    data-date-container="#shipped_at"
                                    data-date-autoclose="true"
                                    data-date-format="M d, yyyy"
                                    data-date-start-date="-1d"
                                    data-date-end-date="+6m"
                                    data-date-today-highlight="true"
                                    required>
                                </th>
                                <th><input type="text" class="form-control"></th>
                                <th><select class="form-control status-select"><option value="all">All</option>@foreach($status as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                <th><input type="text" class="form-control"></th>
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


<x-modal-confirm type="danger" target="purchase-order"></x-modal-confirm>


@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });


    $('.status-select').select2({
        minimumResultsForSearch: Infinity,
    });

    // Default Datatable
    var table = $('#purchase-order-invoices-datatable').DataTable({
        dom: 'Bfrtip',
        stateSave: true,
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
        orderCellsTop: true,
        fixedHeader: true,
        processing: true,
        serverSide: true,
        ajax      : 
            {
                url   : "{{ route('purchase-order-invoices.datatables') }}",
            }, 
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing Orders _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> Orders',
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "columns": [
            { 
                'data': 'invoice_number',
                'orderable': true 
            },
            { 
                'data': 'order_number',
                'orderable': true 
            },
            { 
                'data': 'shipped_at',
                'orderable': true 
            },
            { 
                'data': 'amount',
                'orderable': true 
            },
            { 
                'data': 'status',
                'orderable': true
            },
            { 
                'data': 'user',
                'orderable': true 
            }
            ,
            { 
                'data': 'invoice_number_slug',
                'visible': false,
            }
            
        ],
        "select": {
            "style": "multi"
        },
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#products-datatable_length label').addClass('form-label');
            
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

    $('#purchase-order-invoices-datatable').on('dblclick', 'tr', function () {
        var route = '{{  route("purchase-order-invoices.show", ":id") }}';
        route = route.replace(':id', table.row( this ).data().invoice_number_slug);
        window.location.href = route;
    });


    $('#delete-modal').on('show.bs.modal', function (e) {
        var route = '{{ route("products.delete", ":id") }}';
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-form').attr('action', route);
        }
        
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

@extends('layouts.app')

@section('title')
    @parent() | Credit Notes
@endsection

@section('page-title', 'Credit Notes')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row p-3 bg-secondary mb-3 rounded" >
                    <div class="col-3">
                        <div class="alert alert-danger d-none" role="alert">
                            <span id="date-filter-alert"></span>
                        </div>
                    </div>
                    <div class="col-2 pt-1 text-white" style="text-align: right;">
                        <span>Filter date:</span>
                    </div>
                    <div class="col-1">
                        <select class="date-filter-select form-control" id="filter-column">
                            <option value="created">Created on</option>
                            <option value="confirmed">Confirmed on</option>
                        </select>
                    </div>
                    <div class="col-2 position-relative" id="filter_from">
                        <input type="text" class="form-control" name="filter_from"
                            id="filter-from" 
                            data-provide="datepicker" 
                            data-date-container="#filter_from"
                            data-date-autoclose="true"
                            data-date-format="M d, yyyy"
                            data-date-today-highlight="false"
                            required>
                    </div>
                    <div class="col-2 position-relative" id="filter_to">
                        <input type="text" class="form-control" name="filter_to" 
                            id="filter-to"
                            data-provide="datepicker" 
                            data-date-container="#filter_to"
                            data-date-autoclose="true"
                            data-date-format="M d, yyyy"
                            data-date-today-highlight="false"
                            required>

                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-info" id="btn-apply-filter">Apply</button>
                        <button type="button" class="btn btn-danger" id="btn-clear-filter">Clear All Filters</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-centered table-striped table-bordered table-hover w-100 dt-responsive nowrap table-has-dlb-click" id="credit-notes-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>CN #</th>
                                <th>Warehouse</th>
                                <th>Dealer</th>
                                <th>State</th>
                                <th>Created On</th> 
                                <th>Confirmed On</th> 
                                <th style="text-align: right;">Amount</th>
                                <th style="width:100px;">Status</th> 
                                <th>Created By</th> 
                            </tr>
                            <tr class="filters">
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th id="created_at" class="position-relative no-filter">
                                    <input type="text" class="form-control" name="created_at" 
                                    data-provide="datepicker" 
                                    data-date-container="#created_at"
                                    data-date-autoclose="true"
                                    data-date-format="M d, yyyy"
                                    data-date-start-date="-1d"
                                    data-date-end-date="+6m"
                                    data-date-today-highlight="true"
                                    required>
                                </th>
                                <th id="confirmed_at" class="position-relative no-filter">
                                    <input type="text" class="form-control" name="confirmed_at" 
                                    data-provide="datepicker" 
                                    data-date-container="#confirmed_at"
                                    data-date-autoclose="true"
                                    data-date-format="M d, yyyy"
                                    data-date-start-date="-1d"
                                    data-date-end-date="+6m"
                                    data-date-today-highlight="true"
                                    required>
                                </th>
                                <th class="no-filter"><input type="text" class="form-control"></th>
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


<x-modal-confirm type="danger" target="sale-order"></x-modal-confirm>


@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    let filter_column_val = null
    let filter_from_val = null
    let filter_to_val = null

    $('#btn-apply-filter').on('click', function(e) {
        filter_column_val = $("#filter-column").val();

        let toDate = null
        if ($("#filter-to").val()) {
            let toDate = new Date($("#filter-to").val())
            const offsetTo = toDate.getTimezoneOffset()
            toDate = new Date(toDate.getTime() - (offsetTo*60*1000))
            filter_to_val = toDate.toISOString().split('T')[0]
        }

        let fromDate = null
        if ($("#filter-from").val()) {
            let fromDate = new Date($("#filter-from").val())
            const offsetFrom = fromDate.getTimezoneOffset()
            fromDate = new Date(fromDate.getTime() - (offsetFrom*60*1000))
            filter_from_val = fromDate.toISOString().split('T')[0]
        }


        if ((filter_from_val!==null) && (filter_to_val!==null)) {
            if (fromDate > toDate) {
                $(".alert").show()
                $("#date-filter-alert").text('From date cannot be greater than to date')
                setTimeout( '$(".alert").hide(); $("#filter-from").focus()', 2000)
            }
            else
                table.ajax.reload();
        }
        else {
            $(".alert").show()
            $("#date-filter-alert").text('Dates not specified')
            setTimeout( '$(".alert").hide(); $("#filter-from").focus()', 2000)
        }
    })


    $('#btn-clear-filter').on('click', function(e) {
        $("#filter-from").val('')
        $("#filter-to").val('')
        filter_from_val = null
        filter_to_val = null

        // clears the search values
        table.search('').columns().search('')
        
        // clears the text from the input fields
        table.columns().eq(0).each(function(colIdx) {
            $('input', $('.filters th').eq($(table.column(colIdx).header()).index()) ).val('')
        })

        table.ajax.reload();
    })


    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });


    // Default Datatable
    var table = $('#credit-notes-datatable').DataTable({
        dom: 'Bfrtip',
        stateSave: true,
        //scrollY: "500px",
        paging: true,
        buttons: [
            {
                text: '<i class="mdi mdi-plus-circle me-2"></i> {{ __('app.add_title', ['field' => 'credit note']) }}',
                className: 'btn btn-light   ',
                action: function ( e, dt, node, config ) {
                    window.location.href="{{ route('credit-notes.create') }}"
                }
            },
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
        ],
        orderCellsTop: true,
        fixedHeader: true,

        processing: true,
        serverSide: true,
        ajax: {
            url     : "{{ route('credit-notes.datatables') }}",
            data    : function(d) {
                d.filter_column = filter_column_val,
                d.filter_from = filter_from_val,
                d.filter_to = filter_to_val
            }  
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
                'data': 'credit_note_number',
                'orderable': true 
            },
            { 
                'data': 'warehouse',
                'orderable': true 
            },
            { 
                'data': 'dealer',
                'orderable': true 
            },
            { 
                'data': 'state',
                'orderable': true 
            },
            { 
                'data': 'created_at',
                'orderable': true 
            },
            { 
                'data': 'confirmed_at',
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
            /*
            {
                'data': 'id',
                "width": "5%",
                'className': 'noVis',
                'orderable': false,
                'render' : function(data, type, row, meta){
                    if (type === 'display'){

                        var edit_btn = '';
                        var delete_btn = '';

                        var edit_route = '{{ route("credit-notes.show", ":id") }}';
                        edit_route = edit_route.replace(':id', row.credit_note_number_slug);
                        edit_btn = '<a href="' + edit_route + '" class="action-icon"> <i class="mdi mdi-pencil"></i></a>'                       
                        delete_btn = '<a href="" class="action-icon" id="' + data + '" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>'

                        //data = edit_btn + delete_btn
                        data = edit_btn
                    }
                    return data;
                }
            }
            */            
        ],
        columnDefs: [
            { className: "dt-right", "targets": [6] },
        ],        
        "select": {
            "style": "multi"
        },
        "aaSorting": [[4, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');           
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

    $('#credit-notes-datatable').on('dblclick', 'tr', function () {
        var route = '{{  route("credit-notes.show", ":id") }}';
        route = route.replace(':id', table.row( this ).data().credit_note_number_slug);
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

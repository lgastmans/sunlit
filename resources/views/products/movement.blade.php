<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-centered table-striped table-bordered table-hover w-100 dt-responsive nowrap" id="movement-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Warehouse</th>
                                <th>Quantity</th>
                                <th>Entry</th>
                                <th>Ordered on</th>
                                <th>Created by</th>
                            </tr>
                            <tr class="filters" style="display:none;"> 
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>      
                                <th><input type="text" class="form-control"></th>
                                <th><select class="form-control entry-select">@foreach($entry_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
                                <th id="created_at" class="position-relative">
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


@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    $('.toggle-filters').on('click', function(e) {
        $( ".filters" ).slideToggle('slow');
    });


    var movementTable = $('#movement-datatable').DataTable({
        dom: 'Bfrtip',
        stateSave: true,
        buttons: [
            {
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5]
                },
                className: 'btn btn-success'
            },
            {
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [ 0, 1, 2, 3, 4, 5]
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
        //deferLoading: 0,
        searching: true,
        paging: false,
        ajax      : {
            url   : "{{ route('inventory-movement.datatables') }}",
            "data": function ( d ) {
                d.filter_product_id = {{ $product->id }};
            },
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
                'data': 'order_number',
                'orderable': true
            },
            { 
                'data': 'warehouse',
                'orderable': true
            },
            { 
                'data': 'quantity',
                'orderable': true
            },
            { 
                'data': 'entry_type',
                'orderable': true
            },
            { 
                'data': 'created_at',
                'orderable': true 
            },
            { 
                'data': 'user',
                'orderable': true
            }
        ],
        
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#movement-datatable_length label').addClass('form-label');
            
        },
        
    });

    movementTable.columns().eq(0).each(function(colIdx) {

        var cell = $('.filters th').eq($(movementTable.column(colIdx).header()).index());
        var title = $(cell).text();

        if($(cell).hasClass('no-filter')){

            $(cell).html('&nbsp');

        }
        else{

            // $(cell).html( '<input class="form-control filter-input" type="text"/>' );

            $('select', $('.filters th').eq($(movementTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                //var regexr = '({search})'; //$(this).parents('th').find('select').val();
                movementTable
                    .column(colIdx)
                    .search(this.value) //(this.value != "") ? regexr.replace('{search}', 'this.value') : "", this.value != "", this.value == "")
                    .draw();
                 
            });
            
            $('input', $('.filters th').eq($(movementTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                //var regexr = '({search})'; //$(this).parents('th').find('select').val();
                movementTable
                    .column(colIdx)
                    .search(this.value) //(this.value != "") ? regexr.replace('{search}', 'this.value') : "", this.value != "", this.value == "")
                    .draw();
                 
            }); 
        }
    });

});

    </script>    
@endsection

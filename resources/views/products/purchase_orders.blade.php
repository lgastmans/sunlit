<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
{{--                 <div class="row mb-2">
                    <div class="col-sm-10">
                        
                    </div>
                    <div class="col-sm-2">
                        <div class="text-sm-end">
                           <button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button> 
                        </div>
                    </div><!-- end col-->
                </div>
 --}}
                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="purchase-orders-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Date ordered</th>
                                <th>Order #</th>
                                <th>Quantity Ordered</th>
                                <th>Status</th>
                                <th>Warehouse</th>
                                <th>User</th>
                            </tr>
                            <tr class="filters" style="display:none;">
                                <th id="ordered_at" class="position-relative">
                                    <input type="text" class="form-control" name="ordered_at" 
                                    data-provide="datepicker" 
                                    data-date-container="#ordered_at"
                                    data-date-autoclose="true"
                                    data-date-format="M d, yyyy"
                                    required>
                                </th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><select class="form-control warehouse-select">@foreach($warehouse_filter as $k => $v) <option value={{ $k }}>{{ $v }}</option> @endforeach</select></th>
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



{{-- @section('page-scripts') --}}
@push('page-scripts')

    <script>
        
 $(document).ready(function () {
    "use strict";


console.log('purchase_orders.blade.php');



    var poTable = $('#purchase-orders-datatable').DataTable({
        processing: true,
        serverSide: true,
        //deferLoading: 0,
        searching: false,
        paging: false,
        ajax      : {
            url   : "{{ route('purchase-orders-items.datatables') }}",
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
                'data': 'ordered_at',
                'orderable': true 
            },
            { 
                'data': 'order_number',
                'orderable': true 
            },
            { 
                'data': 'quantity_ordered',
                'orderable': false
            },
            { 
                'data': 'status',
                'orderable': false
            },
            { 
                'data': 'warehouse',
                'orderable': false
            },
            { 
                'data': 'user',
                'orderable': false
            }
        ],
        
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded'); 
        },
        
    });


/*
    poTable.columns().eq(0).each(function(colIdx) {
        var cell = $('.filters th').eq($(poTable.column(colIdx).header()).index());
        var title = $(cell).text();

        if($(cell).hasClass('no-filter')){

            $(cell).html('&nbsp');

        }
        else{
            $('select', $('.filters th').eq($(poTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                poTable
                    .column(colIdx)
                    .search(this.value)
                    .draw();
                 
            });
            
            $('input', $('.filters th').eq($(poTable.column(colIdx).header()).index()) ).off('keyup change').on('keyup change', function (e) {
                e.stopPropagation();
                $(this).attr('title', $(this).val());
                poTable
                    .column(colIdx)
                    .search(this.value)
                    .draw();
                 
            }); 
        }
    });
*/

});

    </script>    
@endpush
{{-- @endsection --}}

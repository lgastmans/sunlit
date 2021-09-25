<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-10">
                        filters: start_period | end_period | “All / Purchase Orders / Sales Orders”
                    </div>
                    <div class="col-sm-2">
                        <div class="text-sm-end">
                           <button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button> 
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="inventory-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Order #</th>
                                <th>Quantity</th>
                                <th>Entry</th>
                                <th>Warehouse</th>
                                <th>User</th>
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

    var table = $('#inventory-datatable').DataTable({
        processing: true,
        serverSide: true,
        // deferLoading: 0,
        searching: false,
        paging: false,
        ajax      : {
            url   : "{{ route('inventory-movement.datatables') }}",
            "data": function ( d ) {
                d.filter_product_id = {{ $product->id }}; //$(" #hidden_product_id ").val();
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
                'data': 'created_at',
                'orderable': true 
            },
            { 
                'data': 'order_number',
                'orderable': true 
            },
            { 
                'data': 'quantity',
                'orderable': false
            },
            { 
                'data': 'entry_type',
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
            $('#inventory-datatable_length label').addClass('form-label');
            
        },
        
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

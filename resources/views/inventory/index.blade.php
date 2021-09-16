@extends('layouts.app')

@section('page-title', 'Inventory')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        &nbsp;
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <!-- <button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button> -->
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="inventory-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Available Stock</th>
                                <th>Ordered Stock</th>
                                <th>Booked Stock</th>
                                <th>Projected Stock</th>
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

<x-modal-confirm type="danger" target="category"></x-modal-confirm>


@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    var table = $('#inventory-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('inventory.datatables') }}",
        
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
                '</select> rows',
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        
        "columns": [
            { 
                'data': 'code',
                'orderable': true 
            },
            { 
                'data': 'name',
                'orderable': true 
            },
            { 
                'data': 'available',
                'orderable': false
            },
            { 
                'data': 'booked',
                'orderable': false
            },
            { 
                'data': 'ordered',
                'orderable': false
            },
            { 
                'data': 'projected',
                'orderable': false
            },
        ],
        
        "select": {
            "style": "multi"
        },
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

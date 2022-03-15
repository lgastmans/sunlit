@extends('layouts.app')

@section('title')
    @parent() | Warehouses
@endsection

@section('page-title')
    Warehouses
@endsection

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        @if (Auth::user()->can('edit warehouses'))
                            <a href="{{ route('warehouses.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> {{ __('app.add_title', ['field' => 'warehouse']) }}</a>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <!-- <button type="button" class="btn btn-light mb-2">Export</button> -->
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="warehouses-datatable">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Email address</th>
                                <th>Phone</th>
                                <th>Actions</th>
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

<x-modal-confirm type="danger" target="warehouse"></x-modal-confirm>

@endsection

@section('page-scripts')
    <script>


 $(document).ready(function () {

    "use strict";


    var table = $('#warehouses-datatable').DataTable({
        stateSave: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('warehouses.datatables') }}",
        
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing warehouses _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> warehouses',
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "columns": [
            { 
                'data': 'name',
                'orderable': true 
            },
            { 
                'data': 'contact_person',
                'orderable': true 
            },
            { 
                'data': 'city',
                'orderable': true 
            },
            { 
                'data': 'state.name',
                'orderable': true 
            },
            { 
                'data': 'email',
                'orderable': true 
            },
            { 
                'data': 'phone',
                'orderable': true 
            },
            {
                'data': 'id',
                'render' : function(data, type, row, meta){
                    if (type === 'display'){

                        var edit_btn = '';
                        var delete_btn = '';

                        @if (Auth::user()->can('edit warehouses'))
                            var edit_route = '{{ route("warehouses.edit", ":id") }}';
                            edit_route = edit_route.replace(':id', data);
                            edit_btn = '<a href="' + edit_route + '" class="action-icon"> <i class="mdi mdi-pencil"></i></a>'                       
                        @endif
                        @if (Auth::user()->can('delete warehouses'))
                            delete_btn = '<a href="" class="action-icon" id="' + data + '" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>'
                        @endif

                        data = edit_btn +  delete_btn
                    }
                    return data;
                }
            }

            
        ],
        "select": {
            "style": "multi"
        },
        "order": [[4, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#warehouses-datatable_length label').addClass('form-label');
            
        },
    });


    $('#warehouses-datatable').on('dblclick', 'tr', function () {
        var route = '{{  route("warehouses.show", ":id") }}';
        route = route.replace(':id', table.row( this ).data().id);
        window.location.href = route;
    });


    $('#delete-modal').on('show.bs.modal', function (e) {
        var route = '{{ route("warehouses.delete", ":id") }}';
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            console.log(route);
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

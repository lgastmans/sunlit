@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        @if (Auth::user()->can('edit users'))
                            <a href="{{ route('users.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> {{ __('app.add_title', ['field' => 'user']) }}</a>
                        @else
                            &nbsp;
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <!-- <button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button> -->
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="users-datatable">
                        <thead class="table-light">
                            <tr>

                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th style="width:10%">
                                    @if (Auth::user()->can('edit users'))
                                    {{ __('app.dt_actions') }}
                                    @else
                                        &nbsp;
                                    @endif
                                    </th>
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

<x-modal-confirm type="danger" target="user"></x-modal-confirm>



@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    var table = $('#users-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.datatables') }}",
        
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing users _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> users',
        },
        "pageLength": 10,
        "columns": [
            { 
                'data': 'name',
                'orderable': true 
            },
            { 
                'data': 'email',
                'orderable': true 
            },
            { 
                'data': 'role',
                'orderable': false 
            },
            {
                'data': 'status',
                'render' : function(data, type, row, meta){
                    if (type === 'display'){
                        if (data == 'disabled'){
                            data = '<span class="badge bg-danger">Disabled</span>';
                        }
                        else{
                            data = '<span class="badge bg-success">Enabled</span>';
                        }
                    }
                    return data;
                },
                'orderable': true,
            },
            {
                'data': 'id',
                'render' : function(data, type, row, meta){
                    if (type === 'display'){

                        var edit_btn = '';
                        var status_btn = '';

                        @if (Auth::user()->can('edit users'))
                            var edit_route = '{{ route("users.edit", ":id") }}';
                            edit_route = edit_route.replace(':id', data);
                            edit_btn = '<a href="' + edit_route + '" class="action-icon"> <i class="mdi mdi-pencil"></i></a>'                       
                        @endif

                        // Enable/disable button
                        @if (Auth::user()->can('delete users'))
                            if (row.status == "enabled"){
                                status_btn = '<a href="" class="action-icon" id="' + data + '" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="mdi mdi-lock"></i></a>'
                            }
                            if (row.status == "disabled"){
                                var status_route = '{{ route("users.enable", ":id") }}';
                                status_route = status_route.replace(':id', data);
                                status_btn = '<a href="' + status_route + '" class="action-icon"><i class="mdi mdi-lock-open-variant-outline"></i></a>'
                            }
                        @endif

                        data = edit_btn +  status_btn

                    }
                    return data;
                }
            }
            
        ],
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#users-datatable_length label').addClass('form-label');
            
        },
    });

    $('#delete-modal').on('show.bs.modal', function (e) {
        var route = '{{ route("users.delete", ":id") }}';
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

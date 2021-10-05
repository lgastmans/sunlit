@extends('layouts.app')

@section('title')
    @parent() | Categories
@endsection

@section('page-title', 'Categories')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        @if (Auth::user()->can('edit categories'))
                            <a href="{{ route('categories.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> {{ __('app.add_title', ['field' => 'category']) }}</a>
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
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="categories-datatable">
                        <thead class="table-light">
                            <tr>

                                <th>Name</th>
                                <th>HSN</th>
                                <th style="width:10%">
                                    @if (Auth::user()->can('edit categories'))
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

<x-modal-confirm type="danger" target="category"></x-modal-confirm>


@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    var table = $('#categories-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('categories.datatables') }}",
        
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing categories _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> categories',
        },
        "pageLength": {{ Setting::get('general.grid_rows') }},
        "columns": [
            { 
                'data': 'name',
                'orderable': true 
            },
            { 
                'data': 'hsn_code',
                'orderable': true 
            },
            {
                'data': 'id',
                'render' : function(data, type, row, meta){
                    if (type === 'display'){

                        var edit_btn = '';
                        var delete_btn = '';

                        @if (Auth::user()->can('edit categories'))
                            var edit_route = '{{ route("categories.edit", ":id") }}';
                            edit_route = edit_route.replace(':id', data);
                            edit_btn = '<a href="' + edit_route + '" class="action-icon"> <i class="mdi mdi-pencil"></i></a>'                       
                        @endif
                        @if (Auth::user()->can('delete categories'))
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
        "order": [[1, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#categories-datatable_length label').addClass('form-label');
            
        },
    });

    $('#delete-modal').on('show.bs.modal', function (e) {
        var route = '{{ route("categories.delete", ":id") }}';
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

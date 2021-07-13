@extends('layouts.app')

@section('page-title', 'Suppliers')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> Add Supplier</a>
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <button type="button" class="btn btn-success mb-2 me-1"><i class="mdi mdi-cog"></i></button>
                            <button type="button" class="btn btn-light mb-2 me-1">Import</button>
                            <button type="button" class="btn btn-light mb-2">Export</button>
                        </div>
                    </div><!-- end col-->
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="suppliers-datatable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheck1">
                                        <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                    </div>
                                </th>
                                <th>Contact Person</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Email address</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheck2">
                                        <label class="form-check-label" for="customCheck2">&nbsp;</label>
                                    </div>
                                </td>
                                <td class="table-user">
                                    <img src="assets/images/users/avatar-4.jpg" alt="table-user" class="me-2 rounded-circle">
                                    <a href="javascript:void(0);" class="text-body fw-semibold">Paul J. Friend</a>
                                </td>
                                <td>
                                    Homovee
                                </td>
                                <td>
                                    <span class="fw-semibold">128</span>
                                </td>
                                <td>
                                    bob@sunlite.com
                                </td>
                                <td>
                                    07/07/2018
                                </td>
                                

                                <td>
                                    <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                                    <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a>
                                </td>
                            </tr> --}}
                            
                            
                        </tbody>
                    </table>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection

@section('page-scripts')
    <!-- <script src='{{ mix("js/pages/suppliers.js") }}'></script>     -->
    <script>


 $(document).ready(function () {

    "use strict";


    var table = $('#suppliers-datatable').DataTable({
        
        processing: true,
        serverSide: true,
        ajax: "{{ route('suppliers.datatables') }}",
        
        "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            },
            "info": "Showing suppliers _START_ to _END_ of _TOTAL_",
            "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" +
                '<option value="10">10</option>' +
                '<option value="20">20</option>' +
                '<option value="-1">All</option>' +
                '</select> suppliers',
        },
        "pageLength": 10,
        "columns": [
            {
                'data': 'id',
                'orderable': false,
                'render': function (data, type, row, meta) {
                    if (type === 'display') {
                        data = "<div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input dt-checkboxes\"><label class=\"form-check-label\">&nbsp;</label></div>";
                    }
                    return data;
                },
                'checkboxes': {
                    'selectRow': true,
                    'selectAllRender': '<div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input dt-checkboxes\"><label class=\"form-check-label\">&nbsp;</label></div>'
                }
            },
            { 
                'data': 'contact_person',
                'render': function(data){
                    var displayName = '<img width="48" src="/images/users/avatar-'+(Math.floor(Math.random() * 9)+1)+'.jpg" alt="table-user" class="me-2 rounded-circle">';
                    displayName += '<a href="javascript:void(0);" class="text-body fw-semibold">' + data + '</a>'
                    return displayName;
                },
                'orderable': true 
            },
            { 
                'data': 'company',
                'orderable': true 
            },
            { 
                'data': 'address',
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

            
        ],
        "select": {
            "style": "multi"
        },
        "order": [[4, "desc"]],
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            $('#suppliers-datatable_length label').addClass('form-label');
            
        },
    });
  
});





        

        
    </script>
@endsection

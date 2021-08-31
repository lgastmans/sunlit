@extends('layouts.app')

@section('page-title', 'Purchase Orders')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <a href="{{ route('purchase-orders.create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> Create Purchase Order</a>
                    </div>
                    {{-- <div class="col-sm-8">
                        <div class="text-sm-end">
                            <a class="btn" href="{{ route('export.products') }}"><button type="button" class="btn btn-light mb-2">{{ __('app.export') }}</button></a>
                        </div>
                    </div><!-- end col--> --}}
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-borderless table-hover w-100 dt-responsive nowrap" id="purchase-orders-datatable">
                        <thead class="table-light">
                            <tr>
                                {{-- <th style="width: 20px;">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="customCheck1">
                                        <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                    </div>
                                </th> --}}
                                <th>Order ID</th>
                                <th>Supplier</th>
                                <th>Date</th> 
                                <th>Amount</th>
                                <th>Status</th> 
                                <th>Created By</th> 
                                {{-- <th>Actions</th> --}}
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

    var data = [{
        "id": 1,
        "order_number": "#BM9712",
        "supplier": "Solar Edge",
        "ordered_at": "August 05 2021",
        "amount_usd": "$120,100",
        "status": "ordered",
        "user": "Bob",
      }, {
        "id": 2,
        "order_number": "#BM9711",
        "supplier": "Wairee",
        "ordered_at": "May 01 2021",
        "amount_usd": "$54,354",
        "status": "confirmed",
        "user": "Rishi",
      }, {
        "id": 3,
        "order_number": "#BM9710",
        "supplier": "Solar Edge",
        "ordered_at": "March 22 2021",
        "amount_usd": "$1,523",
        "status": "customs",
        "user": "Quentin",
      }, {
        "id": 4,
        "order_number": "#BM9709",
        "supplier": "Solar Edge",
        "ordered_at": "Jan 31 2021",
        "amount_usd": "$95,000",
        "status": "cleared",
        "user": "Luk",
      }, {
        "id": 5,
        "order_number": "#BM9708",
        "supplier": "Solar Edge",
        "ordered_at": "December 24 2020",
        "amount_usd": "$5,400",
        "status": "received",
        "user": "Rishi",
      }, 
    ];    

    // Default Datatable
    var table = $('#purchase-orders-datatable').DataTable({
        orderCellsTop: true,
        fixedHeader: true,

        processing: true,
        serverSide: true,
        ajax: "{{ route('purchase-orders.datatables') }}",

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
        "pageLength": 10,
        "columns": [
            { 
                'data': 'order_number',
                'orderable': true 
            },
            { 
                'data': 'supplier',
                'orderable': true 
            },
            { 
                'data': 'ordered_at',
                'orderable': true 
            },
            // { 
            //     'data': 'expected_at',
            //     'orderable': true 
            // },
            // { 
            //     'data': 'received_at',
            //     'orderable': true 
            // },
            { 
                'data': 'amount_inr',
                'orderable': true 
            },
            { 
                'data': 'status',
                'orderable': true,
                'render': function(data, type, row, meta){
                    if (type === "display"){
                        var status = "";
                        if (data == 1){
                            status = '<span class="badge badge-secondary-lighten">Draft</span>'
                        }
                        if (data == 2){
                            status = '<span class="badge badge-info-lighten">Ordered</span>'
                        }
                        if (data == 3){
                            status = '<span class="badge badge-primary-lighten">confirmed</span>'
                        }
                        if (data == 4){
                            status = '<span class="badge badge-dark-lighten">Shipped</span>'
                        }
                        if (data == 5){
                            status = '<span class="badge badge-warning-lighten">Customs</span>'
                        }
                        if (data == 6){
                            status = '<span class="badge badge-light-lighten">Cleared</span>'
                        }
                        if (data == 7){
                            status = '<span class="badge badge-success-lighten">Received</span>'
                        }
                    }
                    return status
                }
            },
            { 
                'data': 'user',
                'orderable': true 
            // },
            // {
            //     'data': 'id',
            //     'orderable': false,
            //     'render' : function(data, type, row, meta){
            //         if (type === 'display'){

            //             var edit_btn = '';
            //             var delete_btn = '';

            //             @if (Auth::user()->can('edit purchase ordr'))
            //                 var edit_route = '{{ route("products.edit", ":id") }}';
            //                 edit_route = edit_route.replace(':id', data);
            //                 edit_btn = '<a href="' + edit_route + '" class="action-icon"> <i class="mdi mdi-pencil"></i></a>'                       
            //             @endif
            //             @if (Auth::user()->can('delete products'))
            //                 delete_btn = '<a href="" class="action-icon" id="' + data + '" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>'
            //             @endif

            //             data = edit_btn +  delete_btn
            //         }
            //         return data;
            //     }
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
            $(cell).html( '<input type="text"/>' );
        }
    });

    $('#products-datatable').on('dblclick', 'tr', function () {
        var route = '{{  route("products.show", ":id") }}';
        route = route.replace(':id', table.row( this ).data().id);
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

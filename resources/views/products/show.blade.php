@extends('layouts.app')

@section('title')
    @parent() | {{ $product->part_number }}
@endsection

@section('page-title',)
   Product details {{ $product->part_number }}
@endsection

@section('content')



<div class="row">
    <div class="col-xxl-8 col-lg-6">
        <!-- project card -->
        <div class="card d-block">
            <div class="card-body">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="dripicons-dots-3"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="{{ route('products.edit', $product->id) }}" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Edit</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Delete</a>
                    </div>
                </div>
                <!-- project title-->
                <h3 class="mt-0">
                    {{ $product->name }}
                </h3>
                {{-- @if ($product->inventory->stock_available > $product->minimum_quantity * 2)
                    <div class="badge bg-success text-light mb-3">In stock</div>
                @elseif ($product->inventory->stock_available > $product->minimum_quantity)
                    <div class="badge bg-warning text-light mb-3">Low stock</div>
                @else
                    <div class="badge bg-danger text-light mb-3">Stock below minium</div>
                @endif --}}

                <h5>Description:</h5>

                <p class="text-muted mb-3">
                    {{$product->notes}}
                </p>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>First Added</h5>
                            <p>{{ $product->display_created_at }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Last Ordered</h5>
                            <p>???</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Purchase Price</h5>
                            <p>{{ $product->supplier->currency_code }} {{ $product->purchase_price }}</p>
                        </div>
                    </div>
                    @if ($product->inventory)
                        <div class="col-md-3">
                            <div class="mb-4">
                                <h5>Revenue</h5>
                                <p>{{ __('app.currency_symbol_inr')}} ???</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div> <!-- end card-body--> 
        </div> <!-- end card-->
    </div> <!-- end col -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Supplier Information</h4>
                <h5>{{ $product->supplier->company }}</h5>
                <h6>{{ $product->supplier->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    {{ $product->supplier->address }}<br>
                    @if ($product->supplier->address2)
                       {{ $product->supplier->address2 }}<br>
                    @else
                      &nbsp;<br>
                    @endif
                    {{ $product->supplier->city }}, {{ $product->supplier->zip_code }}<br/>
                    <abbr title="Phone">P:</abbr> {{ $product->supplier->phone }} <br/>
                    <abbr title="Mobile">M:</abbr> {{ $product->supplier->phone2 }} <br/>
                    <abbr title="Mobile">@:</abbr> {{ $product->supplier->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->
</div>

{{-- <div class="row">
    <div class="col-xl-8">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div id="stocks-container">
                            <h4>Stock</h4>
                            <table class="table table-centered mb-0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Warehouse</th>
                                        <th>Available</th>
                                        <th>Booked</th>
                                        <th>Ordered</th>
                                        <th>Landed Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->inventory as $inventory)
    
                                        <tr>
                                            <td>{{ $inventory->warehouse->name }}</td>
                                            <td>{{ $inventory->stock_available }}</td>
                                            <td>{{ $inventory->stock_booked }}</td>
                                            <td>{{ $inventory->stock_ordered }}</td>
                                            <td>{{ __('app.currency_symbol_inr')}} {{ $inventory->landed_cost }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3 d-none">Progress</h5>
                <div dir="ltr">
                    <div id="chart" class="apex-charts"></div>
                </div>
            </div>
        </div>
        <!-- end card-->
    </div>
</div> --}}
    
</div>
<!-- end row -->
<div class="row">
    <div class="col-lg-8">
        
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
            <li class="nav-item">
                <a href="#inventory" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                    <i class="mdi mdi-home-variant d-md-none d-block"></i>
                    <span class="d-none d-md-block">Inventory</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#movement" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                    <i class="mdi mdi-account-circle d-md-none d-block"></i>
                    <span class="d-none d-md-block">Movement</span>
                </a>
            </li>
            <li class="nav-item d-none">
                <a href="#purchase-orders" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                    <span class="d-none d-md-block">Purchase</span>
                </a>
            </li>
            <li class="nav-item d-none">
                <a href="#sales-orders" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                    <span class="d-none d-md-block">Sales</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane show active" id="inventory">
                @include('products.inventory')
            </div>
            <div class="tab-pane" id="movement">
                @include('products.movement')
            </div>
            <div class="tab-pane" id="purchase-orders">
                @include('products.purchase_orders')
            </div>
            <div class="tab-pane" id="sales-orders">
                @include('products.sales_orders')
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <script>
        var options = {
          series: [{
            name: "Purchases",
            data: [10, 41, 35, 51, 49, 62, 69, 91, 148]
        },
        {
            name: "Sales",
            data: [12, 12, 65, 52, 60, 20, 32, 4, 48]
        }],
          chart: {
          height: 350,
          type: 'line',
          zoom: {
            enabled: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'straight'
        },
        title: {
          text: 'Purchase Orders and Sales Orders',
          align: 'left'
        },
        grid: {
          row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
          },
        },
        xaxis: {
          categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

/*
        var movementTable = $('#inventory-movement-datatable').DataTable({
            processing: true,
            serverSide: true,
            // deferLoading: 0,
            searching: false,
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

            },
        });
*/

        

    </script>

@endsection

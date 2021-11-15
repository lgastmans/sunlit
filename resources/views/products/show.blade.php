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
                    {{ $product->part_number }}
                </h3>


                <h5>Description:</h5>

                <p class="text-muted mb-3">
                    {{$product->notes}}
                </p>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Last Purchased</h5>
                            <p>{{ $product->last_purchased_on }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Last Sold</h5>
                            <p>{{ $product->last_sold_on }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Cable Length</h5>
                            <p>Input: {{ empty($product->cable_length_input) ? 'NS' : $product->cable_length_input.' m' }}</p>
                            <p>Output: {{ empty($product->cable_length_output) ? 'NS' : $product->cable_length_output.' m' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Warranty</h5>
                            <p>{{ empty($product->warranty) ? '0' : $product->warranty }} Years</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Actual Weight</h5>
                            <p>{{ empty($product->weight_actual) ? 'NS' : $product->weight_actual.' kg' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Volume Weight</h5>
                            <p>{{ empty($product->weight_volume) ? 'NS' : $product->weight_volume.' kg' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h5>Calculated Weight</h5>
                            <p>{{ empty($product->weight_calculated) ? 'NS' : $product->weight_calculated.' kg' }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                        </div>
                    </div>
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
</div><!-- end row -->

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
            <li class="nav-item">
                <a href="#purchase-orders" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                    <span class="d-none d-md-block">Purchase</span>
                </a>
            </li>
            <li class="nav-item">
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

@push('page-scripts')
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

        // var chart = new ApexCharts(document.querySelector("#chart"), options);
        // chart.render();




        @if(Session::has('success'))
            $.NotificationApp.send("Success","{{ session('success') }}","top-right","","success")
        @endif
        @if(Session::has('error'))
            $.NotificationApp.send("Error","{{ session('error') }}","top-right","","error")
        @endif

    </script>

@endpush

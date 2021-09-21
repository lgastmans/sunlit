@extends('layouts.app')

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
<div class="row">
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
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mt-4">
                            <h4>Movement</h4>
                            <table class="table table-bordered table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Warehouse</th>
                                        <th>Order #</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->movement as $movement)
                                        <tr>
                                            <td>{{ $movement->warehouse->name }}</td>
                                            <td>
                                                @if($movement->purchase_order_id)
                                                    {{ $movement->purchase_order->order_number }}
                                                @else
                                                    {{ $movement->sales_order_id }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($movement->purchase_order_id)
                                                    <span class="badge bg-danger">{{ $movement->quantity}}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $movement->quantity}}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                   
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
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
</div>
    
</div>
<!-- end row -->
<div class="row">
    <div class="col-lg-8">
        
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


    </script>

@endsection

@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')


<div class="row">
    <div class="col-xl-5 col-lg-6">

        <div class="row">

            <div class="col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-cart-plus widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Due Pending Orders</h5>
                        <h3 class="mt-3 mb-3 text-success">8</h3>
                        {{-- <p class="mb-0 text-muted">
                            <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 1.08%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p> --}}
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->


            <div class="col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-cart-plus widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Overdue Pending Orders</h5>
                        <h3 class="mt-3 mb-3 text-danger">25</h3>
                        {{-- <p class="mb-0 text-muted">
                            <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i> 1.08%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p> --}}
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row -->

        <div class="row">
            <div class="col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-pulse widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Stock below minimum</h5>
                        <h3 class="mt-3 mb-3 text-danger">150</h3>
                        {{-- <p class="mb-0 text-muted">
                            <span class="text-danger me-2"><i class="mdi mdi-arrow-down-bold"></i>&nbsp;</span>
                            <span class="text-nowrap">products with stock below minimum</span>
                        </p> --}}
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->

            <div class="col-lg-6">
                <div class="card widget-flat">
                    <div class="card-body">
                        <div class="float-end">
                            <i class="mdi mdi-currency-usd widget-icon"></i>
                        </div>
                        <h5 class="text-muted fw-normal mt-0" title="Growth">Current exchange rate</h5>
                        <h3 class="mt-3 mb-3 text-warning">Rs. 74.17</h3>
                        {{-- <p class="mb-0 text-muted">
                            <span class="text-success me-2"><i class="mdi mdi-arrow-up-bold"></i> 4.87%</span>
                            <span class="text-nowrap">Since last month</span>
                        </p> --}}
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row -->

    </div> <!-- end col -->

    <div class="col-xl-7 col-lg-6">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Sales Report</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Profit</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Action</a>
                    </div>
                </div>
                <h4 class="header-title mb-3">Projections Vs Actuals</h4>

                <div dir="ltr">
                    <div id="high-performing-product" class="apex-charts" data-colors="#727cf5,#e3eaef"></div>
                </div>
                    
            </div> <!-- end card-body-->
        </div> <!-- end card-->

    </div> <!-- end col -->
</div>
<!-- end row -->

{{-- <div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Sales Report</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Profit</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Action</a>
                    </div>
                </div>
                <h4 class="header-title mb-3">Revenue</h4>

                <div class="chart-content-bg">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <p class="text-muted mb-0 mt-3">Current Week</p>
                            <h2 class="fw-normal mb-3">
                                <small class="mdi mdi-checkbox-blank-circle text-primary align-middle me-1"></small>
                                <span>$58,254</span>
                            </h2>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-0 mt-3">Previous Week</p>
                            <h2 class="fw-normal mb-3">
                                <small class="mdi mdi-checkbox-blank-circle text-success align-middle me-1"></small>
                                <span>$69,524</span>
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="dash-item-overlay d-none d-md-block" dir="ltr">
                    <h5>Today's Earning: $2,562.30</h5>
                    <p class="text-muted font-13 mb-3 mt-2">Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui.
                        Etiam rhoncus...</p>
                    <a href="javascript: void(0);" class="btn btn-outline-primary">View Statements
                        <i class="mdi mdi-arrow-right ms-2"></i>
                    </a>
                </div>
                <div dir="ltr">
                    <div id="revenue-chart" class="apex-charts mt-3" data-colors="#727cf5,#0acf97"></div>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col--> --}}
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        {{-- <a href="javascript:void(0);" class="dropdown-item">Sales Report</a> --}}
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                        <!-- item-->
                        {{-- <a href="javascript:void(0);" class="dropdown-item">Profit</a> --}}
                        <!-- item-->
                        {{-- <a href="javascript:void(0);" class="dropdown-item">Action</a> --}}
                    </div>
                </div>
                <h4 class="header-title">Monthly Sales by warehouses</h4>
                <div class="mb-4 mt-4">
                    {{-- <div id="world-map-markers" style="height: 224px"></div> --}}
                </div>

                <h5 class="mb-1 mt-0 fw-normal">Auroville</h5>
                <div class="progress-w-percent">
                    <span class="progress-value fw-bold">72k </span>
                    <div class="progress progress-sm">
                        <div class="progress-bar" role="progressbar" style="width: 72%;" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <h5 class="mb-1 mt-0 fw-normal">Chennai</h5>
                <div class="progress-w-percent">
                    <span class="progress-value fw-bold">39k </span>
                    <div class="progress progress-sm">
                        <div class="progress-bar" role="progressbar" style="width: 39%;" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <h5 class="mb-1 mt-0 fw-normal">Bangalore</h5>
                <div class="progress-w-percent">
                    <span class="progress-value fw-bold">25k </span>
                    <div class="progress progress-sm">
                        <div class="progress-bar" role="progressbar" style="width: 39%;" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <h5 class="mb-1 mt-0 fw-normal">Mumbai</h5>
                <div class="progress-w-percent mb-0">
                    <span class="progress-value fw-bold">61k </span>
                    <div class="progress progress-sm">
                        <div class="progress-bar" role="progressbar" style="width: 61%;" aria-valuenow="61" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
{{-- </div> --}}
<!-- end row -->


{{-- <div class="row"> --}}
    <div class="col-xl-7 col-lg-12 order-xl-1">
        <div class="card">
            <div class="card-body">
                <a href="" class="btn btn-sm btn-link float-end">Export
                    <i class="mdi mdi-download ms-1"></i>
                </a>
                <h4 class="header-title mt-2 mb-3">Top Selling Products</h4>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <tbody>
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Studer 4kW Inverter</h5>
                                    <span class="text-muted font-13">&nbsp;</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.100,000</h5>
                                    <span class="text-muted font-13">Price</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">82</h5>
                                    <span class="text-muted font-13">Quantity</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.8,000,000</h5>
                                    <span class="text-muted font-13">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Battery 28kWh</h5>
                                    <span class="text-muted font-13">&nbsp;</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.250,000</h5>
                                    <span class="text-muted font-13">Price</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">70</h5>
                                    <span class="text-muted font-13">Quantity</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.17,500,000</h5>
                                    <span class="text-muted font-13">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Studer XCOM-LAN</h5>
                                    <span class="text-muted font-13">&nbsp;</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.20,000</h5>
                                    <span class="text-muted font-13">Price</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">64</h5>
                                    <span class="text-muted font-13">Quantity</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.1,280,000</h5>
                                    <span class="text-muted font-13">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Wairee Panel</h5>
                                    <span class="text-muted font-13">&nbsp;</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.10,000</h5>
                                    <span class="text-muted font-13">Price</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">50</h5>
                                    <span class="text-muted font-13">Quantity</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.50,000</h5>
                                    <span class="text-muted font-13">Amount</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Studer Charge Controller</h5>
                                    <span class="text-muted font-13">&nbsp;</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.25,000</h5>
                                    <span class="text-muted font-13">Price</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">20</h5>
                                    <span class="text-muted font-13">Quantity</span>
                                </td>
                                <td>
                                    <h5 class="font-14 my-1 fw-normal">Rs.500,000</h5>
                                    <span class="text-muted font-13">Amount</span>
                                </td>
                            </tr>


                        </tbody>
                    </table>
                </div> <!-- end table-responsive-->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->


</div>
<!-- end row -->

@endsection
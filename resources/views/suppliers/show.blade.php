@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')
<div class="row">
    <div class="col-sm-12">
        <!-- Profile -->
        <div class="card bg-secondary">
            <div class="card-body profile-user-box">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    <img src="/images/users/avatar-2.jpg" alt="" class="rounded-circle img-thumbnail">
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <h4 class="mt-1 mb-1 text-white">{{ $supplier->contact_person }}</h4>
                                    <p class="font-13 text-white-50"> {{ $supplier->company }}</p>

                                    <ul class="mb-0 list-inline text-light">
                                        <li class="list-inline-item me-3">
                                            <h5 class="mb-1">$ 25,184</h5>
                                            <p class="mb-0 font-13 text-white-50">Total Sales</p>
                                        </li>
                                        <li class="list-inline-item">
                                            <h5 class="mb-1">5482</h5>
                                            <p class="mb-0 font-13 text-white-50">Number of Orders</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end col-->

                    <div class="col-sm-4">
                        <div class="text-center mt-sm-0 mt-3 text-sm-end">
                            @can('edit suppliers')
                            <a href="{{ route('suppliers.edit', $supplier->id) }}">
                            <button type="button" class="btn btn-light">
                                <i class="mdi mdi-account-edit me-1"></i> Edit Supplier
                            </button>
                        </a>
                            @endcan
                        </div>
                    </div> <!-- end col-->
                </div> <!-- end row -->

            </div> <!-- end card-body/ profile-user-box-->
        </div><!--end profile/ card -->
    </div> <!-- end col-->
</div>
<!-- end row -->


<div class="row">
    <div class="col-xl-4">
        <!-- Personal-Information -->
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mt-0 mb-3">Seller Information</h4>
                {{-- <p class="text-muted font-13">
                    Hye, I’m Michael Franklin residing in this beautiful world. I create websites and mobile apps with great UX and UI design. I have done work with big companies like Nokia, Google and Yahoo. Meet me or Contact me for any queries. One Extra line for filling space. Fill as many you want.
                </p> --}}

                <hr/>

                <div class="text-start">
                    <p class="text-muted"><strong>Full Name :</strong> <span class="ms-2">{{ $supplier->contact_person }}</span></p>

                    <p class="text-muted"><strong>Phone :</strong><span class="ms-2">{{ $supplier->phone }} / {{ $supplier->phone2 }}</span></p>

                    <p class="text-muted"><strong>Email :</strong> <span class="ms-2">{{ $supplier->email }}</span></p>

                    <p class="text-muted"><strong>Location :</strong> <span class="ms-2">{{ $supplier->address }}, {{ $supplier->city }}, {{ $supplier->state->name }} {{ $supplier->zip_code }}</span></p>

                    @if ($supplier->gstin)
                    <p class="text-muted"><strong>GSTIN :</strong><span class="ms-2">{{ $supplier->gstin }}</span></p>
                    @endif


                </div>
            </div>
        </div>
        <!-- Personal-Information -->

        <!-- Toll free number box-->
        <div class="card text-white bg-info overflow-hidden">
            <div class="card-body">
                <div class="toll-free-box text-center">
                    <h4> <i class="mdi mdi-deskphone"></i> {{ $supplier->phone }}</h4>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
        <!-- End Toll free number box-->

       

    </div> <!-- end col-->

    <div class="col-xl-8">

        <!-- Chart-->
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Orders & Sales</h4>
                <div dir="ltr">
                    <div style="height: 260px;" class="chartjs-chart">
                        <canvas id="high-performing-product"></canvas>
                    </div>
                </div>        
            </div>
        </div>
        <!-- End Chart-->
    </div>
    <div class="col">
        <div class="row">
            <div class="col-sm-4">
                <div class="card tilebox-one">
                    <div class="card-body">
                        <i class="dripicons-basket float-end text-muted"></i>
                        <h6 class="text-muted text-uppercase mt-0">Orders</h6>
                        <h2 class="m-b-20">1,587</h2>
                        <span class="badge bg-primary"> +11% </span> <span class="text-muted">From previous period</span>
                    </div> <!-- end card-body-->
                </div> <!--end card-->
            </div><!-- end col -->

            <div class="col-sm-4">
                <div class="card tilebox-one">
                    <div class="card-body">
                        <i class="dripicons-box float-end text-muted"></i>
                        <h6 class="text-muted text-uppercase mt-0">Sales</h6>
                        <h2 class="m-b-20">$<span>46,782</span></h2>
                        <span class="badge bg-danger"> -29% </span> <span class="text-muted">From previous period</span>
                    </div> <!-- end card-body-->
                </div> <!--end card-->
            </div><!-- end col -->

            <div class="col-sm-4">
                <div class="card tilebox-one">
                    <div class="card-body">
                        <i class="dripicons-jewel float-end text-muted"></i>
                        <h6 class="text-muted text-uppercase mt-0">Product Sold</h6>
                        <h2 class="m-b-20">1,890</h2>
                        <span class="badge bg-primary"> +89% </span> <span class="text-muted">Last year</span>
                    </div> <!-- end card-body-->
                </div> <!--end card-->
            </div><!-- end col -->

        </div>
        <!-- end row -->


        

    <!-- end col -->
    <div class="card">
        <div class="card-body">
            <h4 class="header-title mb-3">Products</h4>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ASOS Ridley High Waist</td>
                            <td>$79.49</td>
                            <td><span class="badge bg-primary">82 Pcs</span></td>
                            <td>$6,518.18</td>
                        </tr>
                        <tr>
                            <td>Marco Lightweight Shirt</td>
                            <td>$128.50</td>
                            <td><span class="badge bg-primary">37 Pcs</span></td>
                            <td>$4,754.50</td>
                        </tr>
                        <tr>
                            <td>Half Sleeve Shirt</td>
                            <td>$39.99</td>
                            <td><span class="badge bg-warning">6 Pcs</span></td>
                            <td>$2,559.36</td>
                        </tr>
                        <tr>
                            <td>Lightweight Jacket</td>
                            <td>$20.00</td>
                            <td><span class="badge bg-danger">184 Pcs</span></td>
                            <td>$3,680.00</td>
                        </tr>
                        <tr>
                            <td>Marco Shoes</td>
                            <td>$28.49</td>
                            <td><span class="badge bg-primary">69 Pcs</span></td>
                            <td>$1,965.81</td>
                        </tr>
                    </tbody>
                </table>
            </div> <!-- end table responsive-->
        </div> <!-- end col-->
    </div> <!-- end row-->
</div>
<!-- end row -->

@endsection
@section('page-scripts')
<script>
    ! function ($) {
    "use strict";

    var Profile = function () {
        this.$body = $("body"),
            this.charts = []
    };

    Profile.prototype.respChart = function (selector, type, data, options) {

        var draw3 = Chart.controllers.bar.prototype.draw;
        Chart.controllers.bar = Chart.controllers.bar.extend({
            draw: function () {
                draw3.apply(this, arguments);
                var ctx = this.chart.chart.ctx;
                var _fill = ctx.fill;
                ctx.fill = function () {
                    ctx.save();
                    ctx.shadowColor = 'rgba(0,0,0,0.01)';
                    ctx.shadowBlur = 20;
                    ctx.shadowOffsetX = 4;
                    ctx.shadowOffsetY = 5;
                    _fill.apply(this, arguments)
                    ctx.restore();
                }
            }
        });

        //default config
        Chart.defaults.global.defaultFontColor = "#8391a2";
        Chart.defaults.scale.gridLines.color = "#8391a2";

        // get selector by context
        var ctx = selector.get(0).getContext("2d");
        // pointing parent container to make chart js inherit its width
        var container = $(selector).parent();

        // this function produce the responsive Chart JS
        function generateChart() {
            // make chart width fit with its container
            var ww = selector.attr('width', $(container).width());
            var chart;
            switch (type) {
                case 'Line':
                    chart = new Chart(ctx, { type: 'line', data: data, options: options });
                    break;
                case 'Doughnut':
                    chart = new Chart(ctx, { type: 'doughnut', data: data, options: options });
                    break;
                case 'Pie':
                    chart = new Chart(ctx, { type: 'pie', data: data, options: options });
                    break;
                case 'Bar':
                    chart = new Chart(ctx, { type: 'bar', data: data, options: options });
                    break;
                case 'Radar':
                    chart = new Chart(ctx, { type: 'radar', data: data, options: options });
                    break;
                case 'PolarArea':
                    chart = new Chart(ctx, { data: data, type: 'polarArea', options: options });
                    break;
            }
            return chart;
        };
        // run function - render chart at first load
        return generateChart();
    },
        // init various charts and returns
        Profile.prototype.initCharts = function () {
            var charts = [];

            //barchart
            if ($('#high-performing-product').length > 0) {
                // create gradient
                var ctx = document.getElementById('high-performing-product').getContext("2d");
                var gradientStroke = ctx.createLinearGradient(0, 500, 0, 150);
                gradientStroke.addColorStop(0, "#fa5c7c");
                gradientStroke.addColorStop(1, "#727cf5");

                var barChart = {
                    // labels: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                    datasets: [
                        {
                            label: "Orders",
                            backgroundColor: gradientStroke,
                            borderColor: gradientStroke,
                            hoverBackgroundColor: gradientStroke,
                            hoverBorderColor: gradientStroke,
                            data: [65, 59, 80, 81, 56, 89, 40, 32, 65, 59, 80, 81]
                        },
                        {
                            label: "Revenue",
                            backgroundColor: "#e3eaef",
                            borderColor: "#e3eaef",
                            hoverBackgroundColor: "#e3eaef",
                            hoverBorderColor: "#e3eaef",
                            data: [89, 40, 32, 65, 59, 80, 81, 56, 89, 40, 65, 59]
                        }
                    ]
                };
                var barOpts = {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display: false,
                                color: "rgba(0,0,0,0.05)"
                            },
                            stacked: false,
                            ticks: {
                                stepSize: 20
                            }
                        }],
                        xAxes: [{
                            barPercentage: 0.7,
                            categoryPercentage: 0.5,
                            stacked: false,
                            gridLines: {
                                color: "rgba(0,0,0,0.01)"
                            }
                        }]
                    }
                };

                charts.push(this.respChart($("#high-performing-product"), 'Bar', barChart, barOpts));
            }
        },

        //initializing various components and plugins
        Profile.prototype.init = function () {
            var $this = this;
            // font
            Chart.defaults.global.defaultFontFamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';

            // init charts
            $this.charts = this.initCharts();

            // enable resizing matter
            $(window).on('resize', function (e) {
                $.each($this.charts, function (index, chart) {
                    try {
                        chart.destroy();
                    }
                    catch (err) {
                    }
                });
                $this.charts = $this.initCharts();
            });
        },

        //init flotchart
        $.Profile = new Profile, $.Profile.Constructor = Profile
}(window.jQuery),

    //initializing Profile
    function ($) {
        "use strict";
        $.Profile.init()
    }(window.jQuery);
</script>
@endsection


@extends('layouts.app')

@section('title')
    @parent() | Profile 
@endsection

@section('page-title')
    Profile
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        <!-- Profile -->
        <div class="card bg-primary">
            <div class="card-body profile-user-box">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-lg">
                                    {{-- <img src="/images/users/avatar-2.jpg" alt="" class="rounded-circle img-thumbnail"> --}}
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <h4 class="mt-1 mb-1 text-white">{{ ucfirst($user->name) }}</h4>
                                    <p class="font-13 text-white-50"> {{ $user->role }}</p>

                                    <ul class="mb-0 list-inline text-light">
                                        <li class="list-inline-item me-3">
                                            <h5 class="mb-1">$ 25,184</h5>
                                            <p class="mb-0 font-13 text-white-50">Total Revenue</p>
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
                            <a href="{{ route('profile-edit') }}">
                                <button type="button" class="btn btn-light">
                                    <i class="mdi mdi-account-edit me-1"></i>Edit Profile
                                </button>
                            </a>
                        </div>
                    </div> <!-- end col-->
                </div> <!-- end row -->

            </div> <!-- end card-body/ profile-user-box-->
        </div><!--end profile/ card -->
    </div> <!-- end col-->

     <div class="row">
                            <div class="col-xl-4">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card tilebox-one">
                                            <div class="card-body">
                                                <i class="dripicons-basket float-end text-muted"></i>
                                                <h6 class="text-muted text-uppercase mt-0">Orders</h6>
                                                <h2 class="m-b-20">1,587</h2>
                                                <span class="badge bg-primary"> +11% </span> <span class="text-muted">From previous period</span>
                                            </div> <!-- end card-body-->
                                        </div> <!--end card-->
                                    </div><!-- end col -->

                                    <div class="col-sm-6">
                                        <div class="card tilebox-one">
                                            <div class="card-body">
                                                <i class="dripicons-box float-end text-muted"></i>
                                                <h6 class="text-muted text-uppercase mt-0">Revenue</h6>
                                                <h2 class="m-b-20">$<span>46,782</span></h2>
                                                <span class="badge bg-danger"> -29% </span> <span class="text-muted">From previous period</span>
                                            </div> <!-- end card-body-->
                                        </div> <!--end card-->
                                    </div><!-- end col -->

                                    <div class="col-sm-6">
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

                                
                            </div> <!-- end col-->

                            <div class="col-xl-8">

                                <!-- Chart-->
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-3">Orders & Revenue</h4>
                                        <div dir="ltr">
                                            <div style="height: 260px;" class="chartjs-chart">
                                                <canvas id="high-performing-product"></canvas>
                                                Loading graph...
                                            </div>
                                        </div>        
                                    </div>
                                </div>
                                <!-- End Chart-->



                            </div>
                            <!-- end col -->
                        </div>
</div>
<!-- end row -->




@endsection



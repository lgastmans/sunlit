@section('title', @env('APP_NAME'))

@include('layouts/header')

<div class="content-container">
    <div class="wrapper">
        @include('layouts/left-sidebar')

        <div class="content-page">
            <div class="content">
                
                @include('layouts/top-bar')
                <div class="container-fluid">
                    
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                        @if (Request::segment(1))
                                            <li class="breadcrumb-item"><a href="{{ route(Request::segment(1)) }}">{{ ucfirst(Request::segment(1)) }}</a></li>
                                        @endif
                                        @if (Request::segment(3))
                                            <li class="breadcrumb-item active">{{ ucfirst(Request::segment(3)) }}</li>
                                        @endif
                                        @if (!is_int(Request::segment(2)) && !Request::segment(3))
                                            <li class="breadcrumb-item active">{{ ucfirst(Request::segment(2)) }}</li>
                                        @endif
                                    </ol>
                                </div>
                                <h4 class="page-title">@yield('page-title')</h4>
                            </div>
                        </div>
                    </div>     
                    <!-- end page title --> 
                    
                </div> <!-- container -->

                @yield('content')

            </div>

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            2022 - <script>document.write(new Date().getFullYear())</script> © Sunlit Future, Auroville
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
    </div>
    
    <!-- Right Sidebar -->
    {{-- @include('layouts/right-sidebar')
    <div class="rightbar-overlay"></div> --}}
</div>

@include('layouts/footer')

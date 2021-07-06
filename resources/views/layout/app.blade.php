@section('title', 'Sunlit | Inventory')

@include('layout/header')

<div class="content-container">
    <div class="wrapper">
        @include('layout/left-sidebar')

        <div class="content-page">
            <div class="content">
                
                @include('layout/top-bar')
                <div class="container-fluid">
                    
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
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
                            <script>document.write(new Date().getFullYear())</script> © Sunlit
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
    </div>
    
    <!-- Right Sidebar -->
    @include('layout/right-sidebar')
    <div class="rightbar-overlay"></div>
</div>

@include('layout/footer')

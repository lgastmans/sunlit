@section('title', 'Sunlit | Inventory')

@include('layouts/header')

<div class="content-container">
    <div class="wrapper">

            <div class="content">
                
                <div class="container-fluid" >
                    
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
            <footer class="footer" style="left:0; right: 0;">
                <div class="container-fluid">
                    <div class="row">
                        <script>document.write(new Date().getFullYear())</script> © Sunlit
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
       
    </div>

</div>

@include('layouts/footer')

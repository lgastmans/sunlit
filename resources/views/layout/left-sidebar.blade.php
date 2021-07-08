<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">
    
    <!-- LOGO -->
    <a href="index.html" class="logo text-center logo-light">
        <span class="logo-lg">
            <img src="/images/logo.png" alt="" height="16">
        </span>
        <span class="logo-sm">
            <img src="/images/logo_sm.png" alt="" height="16">
        </span>
    </a>

    <!-- LOGO -->
    <a href="index.html" class="logo text-center logo-dark">
        <span class="logo-lg">
            <img src="/images/logo-dark.png" alt="" height="16">
        </span>
        <span class="logo-sm">
            <img src="/images/logo_sm_dark.png" alt="" height="16">
        </span>
    </a>

    <div class="h-100" id="leftside-menu-container" data-simplebar>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    {{-- <span class="badge bg-success float-end">4</span> --}}
                    <span> Dashboard </span>
                </a>
                {{-- <div class="collapse" id="sidebarDashboards">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="dashboard-analytics.html">Analytics</a>
                        </li>
                        <li>
                            <a href="dashboard-crm.html">CRM</a>
                        </li>
                        <li>
                            <a href="index.html">Ecommerce</a>
                        </li>
                        <li>
                            <a href="dashboard-projects.html">Projects</a>
                        </li>
                    </ul>
                </div> --}}
            </li>

            <li class="side-nav-title side-nav-item">Components</li>

            <li class="side-nav-item">
                <a href="#" class="side-nav-link">
                    <i class="uil-shop"></i>
                    <span> Products </span>
                </a>
            </li>

            <li class="side-nav-item {{ (Request::is('suppliers*') ? ' menuitem-active' : '') }}">
                <a href="{{ route('suppliers')}}" class="side-nav-link {{ (Request::is('suppliers*') ? ' active' : '') }}">
                    <i class="uil-truck-loading"></i>
                    <span> Suppliers </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="#" class="side-nav-link">
                    <i class="uil-users-alt"></i>
                    <span> Dealers </span>
                </a>
            </li>

        </ul>

        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
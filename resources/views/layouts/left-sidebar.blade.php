<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">
    
    <!-- LOGO -->
    <a href="{{ route('home') }}" class="logo text-center bg-sunlit w-80">
        <span class="logo-lg bg-sunlit">
            <img src="/images/logo.png" alt="" height="48">
        </span>
        <span class="logo-sm">
            <img src="/images/logo_sm.png" alt="" height="16">
        </span>
    </a>

    <div class="h-100" id="leftside-menu-container" data-simplebar>

        <!--- Sidemenu -->
        <ul class="side-nav">
            <li class="side-nav-item {{ (Request::is('dashboard*') ? ' menuitem-active' : '') }}">
                <a href="{{ route('dashboard')}}" class="side-nav-link {{ (Request::is('dashboard*') ? ' active' : '') }}">
                    <i class="uil-shop"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            <li class="side-nav-item {{ (Request::is('purchase-orders*') ? ' menuitem-active' : '') }}">
                <a href="{{ route('purchase-orders')}}" class="side-nav-link {{ (Request::is('purchase-orders*') ? ' active' : '') }}">
                    <i class="mdi mdi-cube-outline"></i>
                    <span> Purchase Orders </span>
                </a>
            </li>
            {{-- <li class="side-nav-item">
               <a data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    <span class="badge bg-success float-end">4</span>
                    <span> Dashboard </span>
                </a>
                <div class="collapse" id="sidebarDashboards">
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
                </div> 
            </li> --}}

            <li class="side-nav-title side-nav-item">Components</li>

            <li class="side-nav-item {{ (Request::is('products*') ? ' menuitem-active' : '') }}">
                <a href="{{ route('products')}}" class="side-nav-link {{ (Request::is('products*') ? ' active' : '') }}">
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

            <li class="side-nav-item {{ (Request::is('dealers*') ? ' menuitem-active' : '') }}">
                <a href="{{ route('dealers')}}" class="side-nav-link {{ (Request::is('dealers*') ? ' active' : '') }}">
                    <i class="uil-users-alt"></i>
                    <span> Dealers </span>
                </a>
            </li>
            <li class="side-nav-item {{ (Request::is('warehouses*') ? ' menuitem-active' : '') }}">
                <a href="{{ route('warehouses')}}" class="side-nav-link {{ (Request::is('warehouses*') ? ' active' : '') }}">
                    <i class="mdi mdi-store"></i>
                    <span> Warehouses </span>
                </a>
            </li>


            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarSettings" aria-expanded="false" aria-controls="sidebarSettings" class="side-nav-link">
                    <i class="uil-server"></i>
                    <span> Settings </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarSettings">
                    <ul class="side-nav-second-level">
                        @can('list taxes')
                        <li>
                            <a href="{{ route('taxes')}}" {{ (Request::is('taxes*') ? 'class=" active"' : '') }}>
                                <span> Taxes </span>
                            </a>
                        </li>
                        @endcan
                        @can('list categories')
                        <li>
                            <a href="{{ route('categories')}}" {{ (Request::is('categories*') ? 'class=" active"' : '') }}>
                                <span> Categories </span>
                            </a>
                        </li>
                        @endcan
                        @can('list users')
                        <li>
                            <a href="{{ route('users')}}" {{ (Request::is('users*') ? 'class=" active"' : '') }}>
                                <span> Users </span>
                            </a>
                        </li>
                        @endcan
                        @can('edit settings')
                        <li>
                            <a href="{{ route('settings')}}" {{ (Request::is('settings*') ? 'class=" active"' : '') }}>
                                <span> Global Settings </span>
                            </a>
                        </li>
                        @endcan 
                    </ul>
                </div>
            </li>

        </ul>

        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
<div class="app-menu navbar-menu">
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu"></span></li>
                 <li class="nav-item">
                    <a class="nav-link menu-link @if (request()->routeIs('home')) {{ 'active' }} @endif"
                        href="{{ route('home') }}">
                        <i class="mdi mdi-speedometer"></i>
                        <span data-key="t-dashboards">Dashboards</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.customer.index') }}" class="nav-link {{ request()->is('admin/customer*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Customer</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.product-category.index') }}" class="nav-link {{ request()->is('admin/product-category*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Product Category</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.product.index') }}" class="nav-link {{ request()->is('admin/product*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Product</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.showroom.index') }}" class="nav-link {{ request()->is('admin/showroom*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Showroom</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.user-showroom.index') }}" class="nav-link {{ request()->is('admin/user-showroom*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>User Showroom</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('store.leads.index') }}" class="nav-link {{ request()->is('store-manager/leads*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tag"></i>
                        <p>Leads</p>
                    </a>
                </li>






                             @if(auth()->user() && optional(auth()->user()->crmRole)->slug === 'storemanager')
                              <li class="nav-item">
                                                    <a class="nav-link menu-link {{ request()->routeIs('store.leads.*') ? 'active' : '' }}" href="{{ route('store.leads.index') }}">
                        <i class="mdi mdi-account-group"></i> <span>Lead Management</span>
                    </a>
                              </li>
                @endif
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
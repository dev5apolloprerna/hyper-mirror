@php($roleSlug = optional(auth()->user())->crmRole->slug ?? null)
 <div class="app-menu navbar-menu">
     <div id="scrollbar">
         <div class="container-fluid">
             <div id="two-column-menu"></div>
             <ul class="navbar-nav" id="navbar-nav">
                 <li class="menu-title"><span data-key="t-menu"></span></li>

                 <li class="nav-item">

                    <a class="nav-link menu-link @if (request()->routeIs('home')) active @endif" href="{{ route('home') }}">
                        <i class="mdi mdi-speedometer"></i>
                        <span>Dashboard</span>
                     </a>
                 </li>
 
                @if(!$roleSlug)
                    <li class="nav-item">
                        <a href="{{ route('admin.product-category.index') }}" class="nav-link {{ request()->is('admin/product-category*') ? 'active' : '' }}"><i class="nav-icon fas fa-list"></i><p>Product Category</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.product.index') }}" class="nav-link {{ request()->is('admin/product*') ? 'active' : '' }}"><i class="nav-icon fas fa-box"></i><p>Product</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.showroom.index') }}" class="nav-link {{ request()->is('admin/showroom*') ? 'active' : '' }}"><i class="nav-icon fas fa-store"></i><p>Showroom</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            Users
                        </a>
                    </li>    
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.business') }}" class="nav-link {{ request()->is('admin/reports/business*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            Business Report
                        </a>
                    </li>          
                @else
                    <li class="nav-item">
                        <a href="{{ route('store.leads.index') }}" class="nav-link {{ request()->is('store-manager/leads*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p class="mb-0">{{ $roleSlug === 'storemanager' ? 'Sales Management' : ucfirst($roleSlug) . ' Queue' }}</p>
                            <!-- <p class="mb-0">{{ $roleSlug === 'storemanager' ? 'Lead Management' : ucfirst($roleSlug) . ' Queue' }}</p> -->
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('store.invoice.index') }}"
                           class="nav-link {{ request()->is('store-manager/invoice*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p class="mb-0">Invoices</p>
                        </a>
                    </li>

                 @endif
             </ul>
         </div>
     </div>

     <div class="sidebar-background"></div>
</div>

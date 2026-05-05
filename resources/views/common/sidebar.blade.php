@php($roleSlug = optional(auth()->user())->crmRole->slug ?? null)
<div class="app-menu navbar-menu">
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu"></span></li>

                <li class="nav-item">

                    <a class="nav-link menu-link @if (request()->routeIs('home')) active @endif"
                        href="{{ route('home') }}">
                        <i class="mdi mdi-speedometer"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @if (!$roleSlug)
                <li class="nav-item">
                    <a class="nav-link" href="#sidebarMore" data-bs-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="sidebarMore">
                        <i class="ri-briefcase-2-line text-white"></i> Masters</a>
                    <div class="menu-dropdown collapse show" id="sidebarMore" style="">
                        <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.showroom.index') }}"
                            class="nav-link {{ request()->is('admin/showroom*') ? 'active' : '' }}"><i
                                class="nav-icon fas fa-store"></i>Showroom
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.product-category.index') }}"
                            class="nav-link {{ request()->is('admin/product-category*') ? 'active' : '' }}"><i
                                class="nav-icon fas fa-list"></i>Product Category
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.product.index') }}"
                            class="nav-link {{ request()->is('admin/product*') ? 'active' : '' }}"><i
                                class="nav-icon fas fa-box"></i>Product
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.product-shape.index') }}"
                            class="nav-link {{ request()->is('admin/product-shape*') ? 'active' : '' }}"><i
                                class="nav-icon fas fa-draw-polygon"></i>Product Shape
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.product-feature.index') }}"
                            class="nav-link {{ request()->is('admin/product-feature*') ? 'active' : '' }}"><i
                                class="nav-icon fas fa-star"></i>Product Feature
                        </a>
                    </li>
                   
                    <li class="nav-item">
                        <a href="{{ route('admin.quotation-cancel-reason.index') }}"
                            class="nav-link {{ request()->is('admin/quotation-cancel-reason*') ? 'active' : '' }}"><i
                                class="nav-icon fas fa-ban"></i>Quotation Cancel Reason
                        </a>
                    </li>
                    </ul>
                </div>
                

                   
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}"
                            class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-cog"></i>
                            Users
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('Paymentcollection.index') }}"
                            class="nav-link {{ request()->is('Paymentcollection/index*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p class="mb-0">Payment</p>
                        </a>
                    </li>
                     
                    <li class="nav-item">
                        <a href="{{ route('store.invoice.index') }}"
                            class="nav-link {{ request()->is('store-manager/invoice*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p class="mb-0">Retail Business</p>
                        </a>
                    </li>
                     <!-- <li class="nav-item">
                        <a href="{{ route('admin.invoice-settings.edit') }}"
                            class="nav-link {{ request()->is('admin/invoice-settings*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-contract"></i>
                            Invoice PDF Settings
                        </a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a href="{{ route('store.invoice.index') }}"
                            class="nav-link {{ request()->is('store-manager/invoice*') ? 'active' : '' }}">
                            <i class="fa-solid fa-briefcase"></i>
                            <p class="mb-0">Retail Business</p>
                        </a>
                    </li> -->
                <li class="nav-item">
                    <a class="nav-link" href="#sidebarMore" data-bs-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="sidebarMore">
                        <i class="ri-briefcase-2-line text-white"></i> Reports</a>
                    <div class="menu-dropdown collapse show" id="sidebarMore" style="">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.reports.business') }}"
                                    class="nav-link {{ request()->is('admin/reports/business*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    Business Report
                                </a>
                            </li>
                             <li class="nav-item">
                                <a href="{{ route('admin.reports.party') }}"
                                    class="nav-link {{ request()->is('admin/reports/party*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    Party Report
                                </a>
                            </li>
                             <li class="nav-item">
                                <a href="{{ route('admin.reports.leads') }}"
                                    class="nav-link {{ request()->is('admin/reports/leads*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-tag"></i>
                                    Lead Report
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('store.leads.index') }}"
                            class="nav-link {{ request()->is('store-manager/leads*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tag"></i>
                            <p class="mb-0">
                                {{ $roleSlug === 'storemanager' ? 'Sales Management' : ucfirst($roleSlug) . ' Queue' }}
                            </p>
                            <!-- <p class="mb-0">{{ $roleSlug === 'storemanager' ? 'Lead Management' : ucfirst($roleSlug) . ' Queue' }}</p> -->
                        </a>
                    </li>
                @endif
                @if ($roleSlug != 'admin')
                    <li class="nav-item">
                        <a href="{{ route('complaints.index') }}"
                            class="nav-link {{ request()->is('complaints*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-comment-dots"></i>
                            <p class="mb-0">Complaint Master</p>
                        </a>
                    </li>
                @endif
                @if (in_array($roleSlug, ['storemanager', 'admin'], true))
                    <li class="nav-item">
                        <a href="{{ route('store.invoice.index') }}"
                            class="nav-link {{ request()->is('store-manager/invoice*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p class="mb-0">Invoices</p>
                        </a>
                    </li>
                    
                @endif
                @if ($roleSlug === 'account')
                    <li class="nav-item">
                        <a href="{{ route('Accountuser.Accountpayments') }}"
                            class="nav-link {{ request()->is('Accountuser/Accountpayments*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p class="mb-0">Paymentcollection</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('Accountuser.reports.party') }}"
                            class="nav-link {{ request()->is('Accountuser/reports/party*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p class="mb-0">Party Report</p>
                        </a>
                    </li>
                @endif
                 @if(in_array($roleSlug, ['storemanager', 'account','fitting'], true))
                    <li class="nav-item">
                        <a href="{{ route('store.ledger.index') }}"
                           class="nav-link {{ request()->is('store-manager/ledger*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p class="mb-0">Ledger History</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

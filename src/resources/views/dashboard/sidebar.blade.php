<div class="offcanvas offcanvas-start sneat-offcanvas" tabindex="-1" id="sneatSidebar">
    <div class="offcanvas-header">
    <div class="brand-link">
        <div class="brand-logo">
        <i class="bx bx-palette"></i>
        </div>
        <span class="brand-text">Sneat</span>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
    <nav class="sidebar-nav">
        <ul class="sneat-sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="menu-item">
                <a href="#" class="menu-link active">
                <i class="menu-icon bx bx-home"></i>
                <span class="menu-text">Dashboard</span>
                </a>
            </li>
            {{-- <li class="menu-item">
                <a href="#" class="menu-link">
                <i class="menu-icon bx bx-chart"></i>
                <span class="menu-text">Analytics</span>
                </a>
            </li> --}}

            <li class="menu-header">Management</li>
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#userMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-user"></i>
                <span class="menu-text">User Management</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="userMenu">
                <li class="menu-item">
                    <a href="#" class="menu-link active" id="sidebar_list_user">
                    <span class="menu-text">All Users</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_user">
                    <span class="menu-text">Add New User</span>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="#" class="menu-link">
                    <span class="menu-text">User Roles</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                    <span class="menu-text">Permissions</span>
                    </a>
                </li> --}}
                </ul>
            </li>

            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#customerMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-user-circle"></i>
                <span class="menu-text">Customer</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="customerMenu">
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_register_customer">
                    <span class="menu-text">Customer</span>
                    </a>
                </li>
                </ul>
            </li>

            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#kendaraanMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-car"></i>
                <span class="menu-text">Kendaraan</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="kendaraanMenu">
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_kendaraan">
                    <span class="menu-text">Kendaraan</span>
                    </a>
                </li>
                </ul>
            </li>
            {{-- Extra Spasi --}}
            <li><br></li>
            {{-- End Of Extra Spasi --}}
        </ul>
    </nav>
    </div>
</div>

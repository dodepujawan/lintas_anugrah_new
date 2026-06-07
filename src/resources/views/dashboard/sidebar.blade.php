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

            @canany([
                'customer.view'
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#customerMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-user-circle"></i>
                <span class="menu-text">Customer</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="customerMenu">
                @can('customer.view')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_register_customer">
                    <span class="menu-text">Customer</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            @endcanany

            @canany([
                'kendaraan.view'
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#kendaraanMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-car"></i>
                <span class="menu-text">Kendaraan</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="kendaraanMenu">
                @can('kendaraan.view')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_kendaraan">
                    <span class="menu-text">Kendaraan</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            @endcanany

            @canany([
                'driver.view'
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#driverMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-user-pin"></i>
                <span class="menu-text">Driver</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="driverMenu">
                @can('driver.view')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_driver">
                    <span class="menu-text">Driver</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            @endcanany

            @canany([
                'price.expedisi',
                'price.customer',
                'price.rent_dingin',
                'price.customer_rent'
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#priceMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-purchase-tag"></i>
                <span class="menu-text">Price</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="priceMenu">
                @can('price.expedisi')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_prices">
                    <span class="menu-text">Ekspedisi</span>
                    </a>
                </li>
                @endcan
                @can('price.customer')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_prices_customer">
                    <span class="menu-text">Ekspedisi Customer</span>
                    </a>
                </li>
                @endcan
                   <li class="menu-item">
                        <div class="dropdown-divider my-2"></div>
                    </li>
                @can('price.rent_dingin')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_rent">
                    <span class="menu-text">Rent</span>
                    </a>
                </li>
                @endcan
                @can('price.customer_rent')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_rent_customer">
                    <span class="menu-text">Rent Customer</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            @endcanany

            @canany([
                'penjualan.expedisi',
                'penjualan.invoice',
                'penjualan.invoice_generate',
                'penjualan.kwitansi',
                'penjualan.rent_dingin',
                'penjualan.invoice_rent_dingin',
                'penjualan.kwitansi_rent_dingin',
                'penjualan.coolroom',
                'penjualan.coolroom_invoice',
                'penjualan.coolroom_kwitansi',
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#penjualanMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-cart"></i>
                <span class="menu-text">Penjualan</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="penjualanMenu">
                @can('penjualan.expedisi')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_expedisi">
                    <span class="menu-text">Ekspedisi</span>
                    </a>
                </li>
                @endcan
                @can('penjualan.invoice')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_invoice_expedisi">
                    <span class="menu-text">Gabung Muatan Expedisi</span>
                    </a>
                </li>
                @endcan
                @can('penjualan.invoice_generate')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_Invoice_gen_expedisi">
                    <span class="menu-text">Invoice Ekspedisi</span>
                    </a>
                </li>
                @endcan
                @can('penjualan.kwitansi')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_Invoice_kwt_expedisi">
                    <span class="menu-text">Kwitansi Ekspedisi</span>
                    </a>
                </li>
                @endcan

                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_Invoice_edit_expedisi">
                    <span class="menu-text">Edit Invoice Ekspedisi</span>
                    </a>
                </li>

                <li class="menu-item">
                    <div class="dropdown-divider my-2"></div>
                </li>
                @can('penjualan.rent_dingin')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_rent_dingin">
                    <span class="menu-text">Sewa Mobil Pendingin</span>
                    </a>
                </li>
                @endcan
                {{-- @can('penjualan.invoice_rent_dingin')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_invoice_rent_dingin">
                    <span class="menu-text">Invoice Sewa Mobil Pendingin</span>
                    </a>
                </li>
                @endcan --}}
                @can('penjualan.invoice_rent_dingin')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_generate_rent_dingin">
                    <span class="menu-text">Invoice Sewa Mobil Pendingin</span>
                    </a>
                </li>
                @endcan
                @can('penjualan.kwitansi_rent_dingin')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_generate_kwt_dingin">
                    <span class="menu-text">Kwitansi Sewa Mobil Pendingin</span>
                    </a>
                </li>
                @endcan

                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_Invoice_edit_rent">
                    <span class="menu-text">Edit Invoice Rent Pendingin</span>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_rent">
                    <span class="menu-text">Rent</span>
                    </a>
                </li> --}}
                </ul>
            </li>
            @endcanany

            @canany([
                'penjualan.coolroom',
                'penjualan.coolroom_invoice',
                'penjualan.coolroom_kwitansi',
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#coolroomMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-fridge"></i>
                <span class="menu-text">Penjualan Coolroom</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="coolroomMenu">
                @can('penjualan.coolroom')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_coolroom">
                    <span class="menu-text">Coolroom</span>
                    </a>
                </li>
                @endcan
                @can('penjualan.coolroom_kwitansi')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_coolroom_inv">
                    <span class="menu-text">Invoice Coolroom</span>
                    </a>
                </li>
                @endcan

                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_coolroom_kwt">
                    <span class="menu-text">Kwitansi Coolroom</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_coolroom_edit">
                    <span class="menu-text">Edit Invoice Coolroom</span>
                    </a>
                </li>
                </ul>
            </li>
            @endcanany

            @canany([
                'supplier.view',
                'service.view'
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#supplierMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-package"></i>
                <span class="menu-text">Supplier</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="supplierMenu">
                @can('supplier.view')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_supplier">
                    <span class="menu-text">Supplier</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#serviceMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-wrench"></i>
                <span class="menu-text">Service</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="serviceMenu">
                @can('service.view')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_service">
                    <span class="menu-text">Service</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            @endcanany

            @canany([
                'user.view',
                'user.create'
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#userMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx bx-user"></i>
                <span class="menu-text">User Management</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="userMenu">
                @can('user.view')
                <li class="menu-item">
                    <a href="#" class="menu-link active" id="sidebar_list_user">
                    <span class="menu-text">All Users</span>
                    </a>
                </li>
                @endcan
                @can('user.create')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_new_user">
                    <span class="menu-text">Add New User</span>
                    </a>
                </li>
                @endcan
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
            @endcanany

            @canany([
                'extra.pajak',
                'extra.rekening',
                'extra.signature',
                'extra.printer',
                'extra.permissions',
                'extra.area',
            ])
            <li class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#extraMenu" role="button" aria-expanded="false">
                <i class="menu-icon bx-slider-alt"></i>
                <span class="menu-text">Extra</span>
                <i class="menu-arrow bx bx-chevron-right"></i>
                </a>
                <ul class="sub-menu collapse" id="extraMenu">
                @can('extra.pajak')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_extra_pajak">
                    <span class="menu-text">Pajak</span>
                    </a>
                </li>
                @endcan
                @can('extra.rekening')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_extra_rekening">
                    <span class="menu-text">Rekening</span>
                    </a>
                </li>
                @endcan
                @can('extra.signature')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_extra_signature">
                    <span class="menu-text">Penanggung Jawab</span>
                    </a>
                </li>
                @endcan
                @can('extra.printer')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_extra_printer">
                    <span class="menu-text">Printer</span>
                    </a>
                </li>
                @endcan
                @can('extra.permissions')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_extra_permissions">
                    <span class="menu-text">Hak Akses</span>
                    </a>
                </li>
                @endcan
                @can('extra.area')
                <li class="menu-item">
                    <a href="#" class="menu-link" id="sidebar_extra_area">
                    <span class="menu-text">Area</span>
                    </a>
                </li>
                @endcan
                </ul>
            </li>
            @endcanany
            {{-- Extra Spasi --}}
            <li><br></li>
            {{-- End Of Extra Spasi --}}
        </ul>
    </nav>
    </div>
</div>
<script>
// Fungsi Auto Close Sidebar Sneat
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sneatSidebar');

    sidebar.addEventListener('click', function(e) {
        // Cari element menu-link yang diklik
        const menuLink = e.target.closest('.menu-link');

        if (menuLink) {
            // Cek apakah ini menu dengan submenu
            const hasSubmenu = menuLink.hasAttribute('data-bs-toggle') &&
                              menuLink.getAttribute('data-bs-toggle') === 'collapse';

            // Jika bukan menu dengan submenu
            if (!hasSubmenu) {
                // Dapatkan instance offcanvas
                const offcanvasInstance = bootstrap.Offcanvas.getInstance(sidebar);

                // Beri sedikit delay agar navigasi tetap berjalan
                setTimeout(() => {
                    if (offcanvasInstance) {
                        offcanvasInstance.hide();
                    }
                }, 150);
            }
        }
    });
});
</script>

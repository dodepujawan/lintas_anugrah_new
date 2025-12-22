@extends('dashboard.index')
@section('content')
<div class="main-master">
    <div class="master-page">
        <h1>Main Page</h1>
        <h2>Lintas Anugrah</h2>
        <div>Expedisi</div>
    </div>
</div>

{{-- Download Loading Modal --}}
<div class="modal fade" id="loading_modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <!-- Gambar loading -->
                {{-- <img src="{{ asset('assets/gambar/loading3.gif') }}" alt="Loading..." class="img-fluid mb-3" style="width: 80px; height: 80px;"> --}}
                <h5 class="fw-bold text-dark mb-2">Memproses Data...</h5>
                <p class="text-muted small mb-0">Harap tunggu sebentar</p>
                <div class="spinner-border text-primary mt-3" role="status" style="width: 2rem; height: 2rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- End Of Download Loading Modal --}}

@endsection
@section('footer')
<script>
$(document).ready(function() {
// ########################### NAVBAR ROOM ##############################################
// ========================= Edit Profile ======================================
    $(document).on('click', '#navbar_edit_profile', function(e) {
        e.preventDefault();
        loadEditProfileForm();
    });

    function loadEditProfileForm() {
        $.ajax({
            url: '{{ route('editregister') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of Edit Profile ======================================
// ========================= Register Customer ======================================
    $(document).on('click', '#sidebar_register_customer', function(e) {
        e.preventDefault();
        loadRegisterCustomerForm();
    });

    function loadRegisterCustomerForm() {
        $.ajax({
            url: '{{ route('index_customer') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of Register Customer ======================================
// ####################### End Of NAVBAR ROOM ###########################################

// +++++++++++++++++++++++++++++++ SIDEBAR ROOM +++++++++++++++++++++++++++++++++++++++++
// ========================= List Register ======================================
    $(document).on('click', '#sidebar_list_user', function(e) {
        e.preventDefault();
        loadListRegisterForm();
    });

    function loadListRegisterForm() {
        $.ajax({
            url: '{{ route('listregister') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of List Register ======================================
// ========================= New Register ======================================
    $(document).on('click', '#sidebar_new_user', function(e) {
        e.preventDefault();
        loadNewRegisterForm();
    });

    function loadNewRegisterForm() {
        $.ajax({
            url: '{{ route('register') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Register ======================================
// ========================= New Kendaraan ======================================
    $(document).on('click', '#sidebar_new_kendaraan', function(e) {
        e.preventDefault();
        loadNewKendaraanForm();
    });

    function loadNewKendaraanForm() {
        $.ajax({
            url: '{{ route('kendaraan.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Kendaraan ======================================
// ========================= New Driver ======================================
    $(document).on('click', '#sidebar_new_driver', function(e) {
        e.preventDefault();
        loadNewDriverForm();
    });

    function loadNewDriverForm() {
        $.ajax({
            url: '{{ route('driver.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Driver ======================================
// ========================= New Prices ======================================
    $(document).on('click', '#sidebar_new_prices', function(e) {
        e.preventDefault();
        loadNewPriceForm();
    });

    function loadNewPriceForm() {
        $.ajax({
            url: '{{ route('price-expedition.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Prices ======================================
// ========================= New Prices Customer ======================================
    $(document).on('click', '#sidebar_prices_customer', function(e) {
        e.preventDefault();
        loadPriceCustomerForm();
    });

    function loadPriceCustomerForm() {
        $.ajax({
            url: '{{ route('price-customer.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Prices Customer ======================================
// ========================= New Price Dingin ======================================
    $(document).on('click', '#sidebar_new_rent', function(e) {
        e.preventDefault();
        loadNewRentForm();
    });

    function loadNewRentForm() {
        $.ajax({
            url: '{{ route('price-rent.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Price Dingin ======================================
// ========================= New Price Dingin ======================================
    $(document).on('click', '#sidebar_rent_customer', function(e) {
        e.preventDefault();
        loadNewRentCusForm();
    });

    function loadNewRentCusForm() {
        $.ajax({
            url: '{{ route('price-rentcus.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Price Dingin ======================================
// ========================= New Price Dingin ======================================
    $(document).on('click', '#sidebar_new_expedisi', function(e) {
        e.preventDefault();
        loadNewExpedisiForm();
    });

    function loadNewExpedisiForm() {
        $.ajax({
            url: '{{ route('expedisi.index') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('.master-page').html(response);
            },
            error: function() {
                $('.master-page').html('<p>Error loading form.</p>');
            }
        });
    }
// ========================= End Of New Price Dingin ======================================
// +++++++++++++++++++++++++++ End Of SIDEBAR ROOM ++++++++++++++++++++++++++++++++++++++
});
</script>
@endsection

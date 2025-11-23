@extends('dashboard.index')
@section('content')
<div class="main-master">
    <div class="master-page">
        <h1>Main Page</h1>
        <h2>Aneka Plastik</h2>
        <div>Toko Online Aneka Plastik</div>
    </div>
</div>
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
// +++++++++++++++++++++++++++ End Of SIDEBAR ROOM ++++++++++++++++++++++++++++++++++++++
});
</script>
@endsection

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
{{-- Modal Update Pajak --}}
<div class="modal fade" id="pajakModal" tabindex="-1" aria-labelledby="pajakModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="pajakForm">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="pajakModalLabel">Update Pajak</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal-ppn" class="form-label">Nilai PPN (%)</label>
                        <input type="number" step="0.01" class="form-control" id="modal-ppn" name="ppn" required>
                        <div class="form-text">Masukkan nilai PPN dalam persen (contoh: 11.0 untuk 11%)</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="id" id="modal-id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submit_pajak">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- End Of Modal Update Pajak --}}

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
// ========================= Update Pajak ======================================
    $(document).on('click', '#sidebar_extra_pajak', function(e) {
        e.preventDefault();
        loadDataPajak();
        function loadDataPajak(){
            $.ajax({
                url: '{{ route('get_pajak') }}',
                type: 'GET',
                success: function(response) {
                    let nilai_ppn = response.data.ppn;
                    $('#modal-ppn').val(nilai_ppn);
                },
                error: function() {
                    $('#modal-ppn').val('Error Loading');
                }
            });
        }
        $('#pajakModal').modal('show');
    });
    // ### Submit Pajak ###
    $('#submit_pajak').on('click', function (e) {
    e.preventDefault();
    let ppn_pajak = $('#modal-ppn').val();
        $.ajax({
            url: '{{ route('update_pajak') }}', // Ganti sesuai route di Laravel kamu
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                ppn: ppn_pajak
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: response.message || 'PPN berhasil disimpan!',
                    timer: 2000,
                    showConfirmButton: false
                });
                $('#pajakModal').modal('hide');
            },
            error: function (xhr) {
                console.error('Gagal:', xhr.responseText);
                alert('Gagal menyimpan PPN');
            }
        });
    });
// ========================= End Of Update Pajak ======================================
// +++++++++++++++++++++++++++ End Of SIDEBAR ROOM ++++++++++++++++++++++++++++++++++++++
});
</script>
@endsection

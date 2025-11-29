<style>
    .card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .card-customer-header {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-customer-header h5 {
        color: #007bff;
        margin: 0;
    }

    /* Untuk mobile */
    @media (max-width: 576px) {
        .card-customer-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .card-customer-header > div:last-child {
            align-self: flex-end;
        }
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-customer-header">
                    <div>
                        <h5>Data Customer</h5>
                    </div>
                    <button class="btn btn-primary" id="add-btn-customer">
                        <i class="fas fa-plus"></i> Tambah Customer
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="mcustomer-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Jenis Usaha</th>
                                    <th>Telepon</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan di-load oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="customer-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customer-form">
                <div class="modal-body">
                    <div id="form-errors" class="alert alert-danger d-none"></div>

                    <!-- DATA CUSTOMER -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">DATA CUSTOMER</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kode" class="form-label">KODE <span class="text-danger">*</span><small class="text-muted">(Di-generate otomatis oleh sistem, mohon cek kembali !)</small></label>
                                    <input type="text" class="form-control enter-next" id="kode" name="kode" required data-next="nama" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">NAMA <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control enter-next" id="nama" name="nama" required data-next="jenis_usaha">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jenis_usaha" class="form-label">JENIS USAHA</label>
                                    <input type="text" class="form-control enter-next" id="jenis_usaha" name="jenis_usaha" data-next="telepon">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telepon" class="form-label">TELEPON</label>
                                    <input type="text" class="form-control enter-next" id="telepon" name="telepon" data-next="alamat">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="alamat" class="form-label">ALAMAT</label>
                                    <textarea class="form-control enter-next" id="alamat" name="alamat" rows="2" data-next="desa"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="desa" class="form-label">DESA</label>
                                    <input type="text" class="form-control enter-next" id="desa" name="desa" data-next="kecamatan">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="kecamatan" class="form-label">KECAMATAN</label>
                                    <input type="text" class="form-control enter-next" id="kecamatan" name="kecamatan" data-next="kabupaten">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="kabupaten" class="form-label">KABUPATEN</label>
                                    <input type="text" class="form-control enter-next" id="kabupaten" name="kabupaten" data-next="kota">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="kota" class="form-label">KOTA</label>
                                    <input type="text" class="form-control enter-next" id="kota" name="kota" data-next="fax">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="fax" class="form-label">FAX</label>
                                    <input type="text" class="form-control enter-next" id="fax" name="fax" data-next="kontak">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="kontak" class="form-label">KONTAK</label>
                                    <input type="text" class="form-control enter-next" id="kontak" name="kontak" data-next="email">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="email" class="form-label">EMAIL</label>
                                    <input type="email" class="form-control enter-next" id="email" name="email" data-next="npwp">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="npwp" class="form-label">NPWP</label>
                                    <input type="text" class="form-control enter-next" id="npwp" name="npwp" data-next="top_kredit">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="top_kredit" class="form-label">TOP KREDIT</label>
                                    <input type="text" class="form-control enter-next" id="top_kredit" name="top_kredit" data-next="purchasing_nama">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PURCHASING -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">PURCHASING</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="purchasing_nama" class="form-label">NAMA</label>
                                    <input type="text" class="form-control enter-next" id="purchasing_nama" name="purchasing_nama" data-next="purchasing_email">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="purchasing_email" class="form-label">EMAIL</label>
                                    <input type="email" class="form-control enter-next" id="purchasing_email" name="purchasing_email" data-next="purchasing_extensi_hp">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="purchasing_extensi_hp" class="form-label">EXTENSI HP</label>
                                    <input type="text" class="form-control enter-next" id="purchasing_extensi_hp" name="purchasing_extensi_hp" data-next="data_pajak_nama">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DATA PAJAK -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">DATA PAJAK</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="data_pajak_nama" class="form-label">NAMA</label>
                                    <input type="text" class="form-control enter-next" id="data_pajak_nama" name="data_pajak_nama" data-next="data_pajak_npwp">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="data_pajak_npwp" class="form-label">NPWP</label>
                                    <input type="text" class="form-control enter-next" id="data_pajak_npwp" name="data_pajak_npwp" data-next="data_pajak_alamat">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="data_pajak_alamat" class="form-label">ALAMAT</label>
                                    <textarea class="form-control enter-next" id="data_pajak_alamat" name="data_pajak_alamat" rows="2" data-next="data_pajak_alamat2"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="data_pajak_alamat2" class="form-label">ALAMAT 2</label>
                                    <textarea class="form-control enter-next" id="data_pajak_alamat2" name="data_pajak_alamat2" rows="2" data-next="pemilik_nama"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INFO PEMILIK -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">INFO PEMILIK</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pemilik_nama" class="form-label">NAMA PEMILIK</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_nama" name="pemilik_nama" data-next="pemilik_no_ktp_sim">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pemilik_no_ktp_sim" class="form-label">NO. KTP/SIM</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_no_ktp_sim" name="pemilik_no_ktp_sim" data-next="pemilik_tempat_lahir">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pemilik_tempat_lahir" class="form-label">TEMPAT LAHIR</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_tempat_lahir" name="pemilik_tempat_lahir" data-next="pemilik_tgl_lahir">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pemilik_tgl_lahir" class="form-label">TANGGAL LAHIR</label>
                                    <input type="date" class="form-control enter-next" id="pemilik_tgl_lahir" name="pemilik_tgl_lahir" data-next="pemilik_alamat_rumah">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="pemilik_alamat_rumah" class="form-label">ALAMAT RUMAH</label>
                                    <textarea class="form-control enter-next" id="pemilik_alamat_rumah" name="pemilik_alamat_rumah" rows="2" data-next="pemilik_desa"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="pemilik_desa" class="form-label">DESA</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_desa" name="pemilik_desa" data-next="pemilik_kecamatan">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="pemilik_kecamatan" class="form-label">KECAMATAN</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_kecamatan" name="pemilik_kecamatan" data-next="pemilik_kabupaten">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="pemilik_kabupaten" class="form-label">KABUPATEN</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_kabupaten" name="pemilik_kabupaten" data-next="pemilik_telepon">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="pemilik_telepon" class="form-label">TELEPON</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_telepon" name="pemilik_telepon" data-next="pemilik_fax">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="pemilik_fax" class="form-label">FAX</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_fax" name="pemilik_fax" data-next="pemilik_email">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="pemilik_email" class="form-label">EMAIL</label>
                                    <input type="email" class="form-control enter-next" id="pemilik_email" name="pemilik_email" data-next="pemilik_npwp">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="pemilik_npwp" class="form-label">NPWP</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_npwp" name="pemilik_npwp" data-next="pemilik_agama">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pemilik_agama" class="form-label">AGAMA</label>
                                    <input type="text" class="form-control enter-next" id="pemilik_agama" name="pemilik_agama" data-next="kontak_lain_nama">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KONTAK SELAIN PEMILIK -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">KONTAK SELAIN PEMILIK</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kontak_lain_nama" class="form-label">NAMA</label>
                                    <input type="text" class="form-control enter-next" id="kontak_lain_nama" name="kontak_lain_nama" data-next="kontak_lain_telepon">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kontak_lain_telepon" class="form-label">TELEPON</label>
                                    <input type="text" class="form-control enter-next" id="kontak_lain_telepon" name="kontak_lain_telepon" data-next="accounting_nama">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACCOUNTING -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="mb-3">ACCOUNTING</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="accounting_nama" class="form-label">NAMA</label>
                                    <input type="text" class="form-control enter-next" id="accounting_nama" name="accounting_nama" data-next="accounting_email">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="accounting_email" class="form-label">EMAIL</label>
                                    <input type="email" class="form-control enter-next" id="accounting_email" name="accounting_email" data-next="accounting_extensi_hp">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="accounting_extensi_hp" class="form-label">EXTENSI HP</label>
                                    <input type="text" class="form-control" id="accounting_extensi_hp" name="accounting_extensi_hp">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View -->
<div class="modal fade" id="view-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="view-content">
                <!-- Content will be loaded by AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Set CSRF token in AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // =============================== Initialize DataTables ====================================
    var table = $('#mcustomer-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("customer_get_data") }}',
        // Scroll settings
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        // Responsive settings
        responsive: true,
        autoWidth: false,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'jenis_usaha', name: 'jenis_usaha' },
            { data: 'telepon', name: 'telepon' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    // ============================= End Of Initialize DataTables =================================
    // ===================================== Add button click  =======================================
    $('#add-btn-customer').click(function() {
        $('#customer-form')[0].reset();
        $('#modal-title').text('Tambah Customer');
        $('#customer-form').attr('data-method', 'store');
        $('#customer-modal').modal('show');
        $('#form-errors').addClass('d-none');
        // Focus ke field pertama saat modal dibuka
        $('#customer-modal').on('shown.bs.modal', function () {
            $('#nama').focus();
        });
        // panggil fungsi load kode customer
        load_kode_customer()
        // panggil fungsi next click
        initializeEnterNext();
    });

    function load_kode_customer() {
        $.ajax({
            url: '{{ route('customer_kode') }}', // Route to load the form
            type: 'GET',
            success: function(response) {
                $('#kode').val(response.kode);
            },
            error: function() {
                $('#kode').val('<p>Error loading form.</p>');
            }
        });
    }
    // ================================= End Of Add button click  =================================
    // ===================================== Edit button click =====================================
    $(document).on('click', '.edit-btn-customer', function() {
        var id = $(this).data('id');
        $('#modal-title').text('Edit Customer');
        $('#customer-form').attr('data-method', 'update');
        $('#customer-form').attr('data-id', id);
        $('#form-errors').addClass('d-none');
        // panggil fungsi next click
        initializeEnterNext();
        // Focus ke field pertama saat modal dibuka
        $('#kode').focus();

        // Load data via AJAX
        $.ajax({
            url: '{{ route("customer_show", ["id" => ":id"]) }}'.replace(':id', id),
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    var customer = response.data;
                    // Populate form fields
                    Object.keys(customer).forEach(function(key) {
                        // console.log('Field:', key, 'Value:', customer[key]);
                        if ($('#' + key).length) {
                            let value = customer[key];
                            if ($('#' + key).attr('type') === 'date' && value) {
                                value = value.split('T')[0];
                            }
                            $('#' + key).val(value); // <--- di sini
                        }
                    });
                    $('#customer-modal').modal('show');
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    });
    // =============================== End Of Edit button click ===================================
    // =============================== Function Next Form click ===================================
    function initializeEnterNext() {
        $('.enter-next').off('keypress').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                var nextFieldId = $(this).data('next');
                if (nextFieldId && $('#' + nextFieldId).length) {
                    $('#' + nextFieldId).focus();
                }
            }
        });
    }
    // ============================ End Of Function Next Form click ================================
    // ================================= View button click ===================================
    $(document).on('click', '.view-btn-customer', function() {
        var id = $(this).data('id');

        $.ajax({
            url: '{{ route("customer_show", ["id" => ":id"]) }}'.replace(':id', id),
            type: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    var customer = response.data;
                    var content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>DATA CUSTOMER</h6>
                                <table class="table table-sm">
                                    <tr><th>Kode</th><td>${customer.kode || '-'}</td></tr>
                                    <tr><th>Nama</th><td>${customer.nama || '-'}</td></tr>
                                    <tr><th>Jenis Usaha</th><td>${customer.jenis_usaha || '-'}</td></tr>
                                    <tr><th>Telepon</th><td>${customer.telepon || '-'}</td></tr>
                                    <tr><th>Email</th><td>${customer.email || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>INFO PEMILIK</h6>
                                <table class="table table-sm">
                                    <tr><th>Nama Pemilik</th><td>${customer.pemilik_nama || '-'}</td></tr>
                                    <tr><th>No. KTP/SIM</th><td>${customer.pemilik_no_ktp_sim || '-'}</td></tr>
                                    <tr><th>Email</th><td>${customer.pemilik_email || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;
                    $('#view-content').html(content);
                    $('#view-modal').modal('show');
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memuat data');
            }
        });
    });
    // =============================== End Of View button click =================================
    // ===================================== Form submit ======================================
    $('#customer-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var method = $(this).attr('data-method');
        var id = $(this).attr('data-id');

        var url, httpMethod;

        if (method === 'update') {
            url = '{{ route("customer_update", ["id" => ":id"]) }}'.replace(':id', id);
            httpMethod = 'POST'; // Karena menggunakan POST untuk update dengan method override
        } else {
            url = '{{ route("customer_store") }}';
            httpMethod = 'POST';
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    $('#customer-modal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sukses!', response.success, 'success');
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<ul>';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul>';
                    $('#form-errors').html(errorHtml).removeClass('d-none');
                } else {
                    alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Server error'));
                }
            }
        });
    });
    // ================================= End Of Form submit ===================================
    // ================================= Delete button click =================================
    $(document).on('click', '.delete-btn-customer', function() {
        var id = $(this).data('id');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("customer_destroy", ["id" => ":id"]) }}'.replace(':id', id),
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            table.ajax.reload();

                            Swal.fire({
                                title: 'Terhapus!',
                                text: response.message || 'Data berhasil dihapus',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data';

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
});
    // ============================== End Of Delete button click =================================
    // ================================= Close modal handler =================================
    $('#customer-modal').on('hidden.bs.modal', function () {
        $('#form-errors').addClass('d-none');
    });
    // ============================== End of Close modal handler ==============================
});
</script>

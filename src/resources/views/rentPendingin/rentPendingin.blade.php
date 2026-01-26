<style>
    .card-expedisi {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 15px;
        padding: 20px;
        border: 1px solid #dee2e6;
    }

    .card-expedisi-header {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .card-expedisi-header h5 {
        color: #007bff;
        margin: 0;
        font-weight: 600;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .form-control-sm {
        font-size: 0.875rem;
    }

    .table-sm th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.85rem;
        text-align: center;
    }

    .btn-action {
        font-size: 0.85rem;
        padding: 6px 12px;
        margin: 2px;
    }

    .status-deteksi {
        background-color: #ffc107;
        color: #856404;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        display: inline-block;
    }

    .driver-section {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #f8f9fa;
    }

    .driver-header {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .col-md-2, .col-md-3, .col-md-4 {
            margin-bottom: 10px;
        }
    }

    /* sweetalert show */
    .swal2-container {
        z-index: 2000 !important;
    }

    .form-title {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 10px 15px;
        margin-bottom: 20px;
    }

    .table-custom {
        font-size: 0.875rem;
    }

    .total-section {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-top: 20px;
        border: 1px dashed #dee2e6;
    }

    .function-keys {
        background-color: #e9ecef;
        border-radius: 4px;
        padding: 10px;
        font-size: 0.8rem;
    }
    /* === PERBAIKAN: CONTAINER LEBIH LEBAR === */
    /* .container-fluid {
            max-width: 1600px !important;
            margin: 0 auto !important;
            padding-left: 30px !important;
            padding-right: 30px !important;
            width: 100% !important;
        } */

        /* PERBAIKAN: Kolom lebih lebar */
        /* .col-lg-12 {
            width: 100% !important;
            max-width: 100% !important;
        } */

        /* PERBAIKAN: Input group lebih proporsional */
        /* .input-group-sm {
            height: auto !important;
        } */

        /* PERBAIKAN: Badge lebih besar */
        /* .badge {
            font-size: 0.9rem !important;
            padding: 6px 10px !important;
        } */

        /* PERBAIKAN: Textarea lebih tinggi */
        /* textarea.form-control-sm {
            min-height: 80px !important;
        } */

        /* PERBAIKAN: Hapus batasan width pada kolom */
        /* .col-lg-10, .col-xl-9 {
            max-width: 100% !important;
            flex: 0 0 100% !important;
        } */
</style>
<div class="container-fluid mt-4 mb-5" >
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <!-- Header Form -->
            <div class="form-title">
                <h4 class="mb-1">FORM SEWA MOBIL BERPENDINGIN</h4>
                <p class="text-muted mb-0">Sistem Penyewaan Mobil Berpendingin</p>
            </div>

            <!-- Card Utama -->
            <div class="card-expedisi">
                <div class="card-expedisi-header">
                    <h5>DATA PENYEWAAN</h5>
                </div>

                <!-- Informasi Nomor Muat -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label class="form-label">NO.MUAT</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="no_muat_rent_dingin" name="no_muat_rent_dingin"
                                            class="form-control form-control-sm" placeholder="Auto Generate/ click for update" readonly>
                                    <button class="btn btn-outline-primary border-start-0" id="no_muat_rent_dingin_btn" type="button">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TANGGAL MUAT</label>
                                <input type="date" id="tanggal_rent_dingin" name="tanggal_rent_dingin"
                                        class="form-control form-control-sm">
                            </div>
                            {{-- <div class="col-md-2">
                                <button class="btn btn-sm btn-outline-primary btn-action mt-4">CARI</button>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Informasi Nomor Surjal -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">NO.Surat Jalan</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="no_surjal_rent_dingin" name="no_surjal_rent_dingin"
                                            class="form-control form-control-sm" placeholder="Auto Generate/ click for update" readonly>
                                    <button class="btn btn-outline-primary border-start-0" id="no_surjal_rent_dingin_btn" type="button">
                                        <i class="bx bx-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">TANGGAL SURAT JALAN</label>
                                <input type="date" id="tanggal_surjal_rent_dingin" name="tanggal_surjal_rent_dingin" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">WILAYAH NOSJ</label>
                                <select id="wilayah_nosj_rent_dingin" name="wilayah_nosj_rent_dingin"
                                        class="form-select form-select-sm">
                                    <option selected>DENPASAR</option>
                                    <option>JAKARTA</option>
                                    <option>SURABAYA</option>
                                    <option>BANDUNG</option>
                                </select>
                            </div>
                            {{-- <div class="col-md-2">
                                <button class="btn btn-sm btn-outline-primary btn-action mt-4">CARI</button>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Data Customer -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="driver-section">
                            <div class="driver-header">DATA CUSTOMER</div>
                            <div class="row">
                                <!-- Kiri: Data Customer & Kode -->
                                <div class="col-md-6 mt-2">
                                    <div class="card border-light">
                                        <div class="card-body p-2">
                                            <label class="form-label fw-semibold">CUSTOMER</label>
                                            <div class="input-group input-group-sm mb-2">
                                                <input type="hidden" name="customer_rent_dingin_id" id="customer_rent_dingin_id">
                                                <input type="text" class="form-control" id="customer_rent_dingin"
                                                    name="customer_rent_dingin" readonly
                                                    placeholder="Pilih customer...">
                                                <button class="btn btn-outline-primary" id="customer_rent_dingin_btn" type="button">
                                                    <i class="bx bx-search"></i>
                                                </button>
                                            </div>

                                            <!-- CUSTOMER KODE -->
                                            <div class="customer-kode-info">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-light text-dark me-2">Kode:</span>
                                                    <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                                                        id="customer_kode_dingin" name="customer_kode_dingin"
                                                        readonly style="font-weight: 600;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kanan: Nama Penerima & Telpon dalam satu kolom bertumpuk -->
                                <div class="col-md-6">
                                    <!-- Nama Penerima -->
                                    <div class="mb-3">
                                        <label class="form-label">NAMA PENERIMA</label>
                                        <input type="text" id="nama_penerima_rent_dingin" name="nama_penerima_rent_dingin"
                                                class="form-control form-control-sm"
                                                placeholder="Masukkan nama penerima">
                                    </div>

                                    <!-- Telpon -->
                                    <div>
                                        <label class="form-label">TELEPON PENERIMA</label>
                                        <input type="text" id="telpon_rent_dingin" name="telpon_rent_dingin"
                                                class="form-control form-control-sm"
                                                placeholder="Masukkan nomor telepon">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label class="form-label">ALAMAT PENERIMA</label>
                                    <textarea id="alamat_rent_dingin" name="alamat_rent_dingin"
                                                class="form-control form-control-sm" rows="2"
                                                placeholder="Masukkan alamat customer"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Kendaraan & Driver -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="driver-section">
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label class="form-label">ITEM</label>
                                    <div class="input-group input-group-sm">
                                        <input type="hidden" class="form-control" id="item_rent_dingin_id" name="item_rent_dingin_id" placeholder="Pilih kendaraan...">
                                        <input type="text" class="form-control" id="item_rent_dingin" name="item_rent_dingin" placeholder="Pilih Item..." readonly>
                                        <button class="btn btn-outline-secondary" id="item_rent_dingin_btn"><i class="bx bx-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">JML.HARI</label>
                                    <input type="number" id="jml_hari_rent_dingin" name="jml_hari_rent_dingin"
                                            class="form-control form-control-sm" placeholder="0" min="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">HARGA</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="harga_rent_dingin" name="harga_rent_dingin"
                                                class="form-control form-control-sm" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="driver-section">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">DRIVER</label>
                                    <div class="input-group input-group-sm">
                                        <input type="hidden" class="form-control" id="driver_rent_dingin_id" name="driver_rent_dingin_id">
                                        <input type="text" class="form-control" id="driver_rent_dingin" name="driver_rent_dingin" readonly placeholder="Pilih drver I">
                                        <button class="btn btn-outline-secondary" id="driver_rent_dingin_btn"><i class="bx bx-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">KENDARAAN</label>
                                    <div class="input-group input-group-sm">
                                        <input type="hidden" class="form-control" id="kendaraan_rent_dingin_id" name="kendaraan_rent_dingin_id" placeholder="Pilih kendaraan...">
                                        <input type="text" class="form-control" id="kendaraan_rent_dingin" name="kendaraan_rent_dingin" placeholder="Pilih kendaraan..." readonly>
                                        <button class="btn btn-outline-secondary" id="kendaraan_rent_dingin_btn"><i class="bx bx-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perhitungan Total -->
                <div class="total-section mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label class="form-label">SUB TOTAL</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="sub_total_rent_dingin" name="sub_total_rent_dingin"
                                            class="form-control form-control-sm" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label class="form-label">DISCOUNT</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">%</span>
                                    <input type="text" id="discount_rent_dingin" name="discount_rent_dingin"
                                            class="form-control form-control-sm" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label class="form-label">DPP</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="dpp_rent_dingin" name="dpp_rent_dingin"
                                            class="form-control form-control-sm" value="0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label class="form-label">PAJAK</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">%</span>
                                    <input type="text" id="pajak_rent_dingin" name="pajak_rent_dingin"
                                            class="form-control form-control-sm" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label">KETERANGAN</label>
                                <textarea id="keterangan_rent_dingin" name="keterangan_rent_dingin"
                                            class="form-control form-control-sm" rows="2" placeholder="Masukkan keterangan tambahan"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4 offset-md-4">
                            <div class="mb-2">
                                <label class="form-label fs-5 fw-bold">TOTAL</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text fw-bold">Rp</span>
                                    <input type="text" id="total_rent_dingin" name="total_rent_dingin"
                                            class="form-control form-control-lg fw-bold" value="0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <div class="row justify-content-center mt-4 g-3">
                            <div class="col-md-3 col-sm-6">
                                <button class="btn btn-info btn-sm w-100 py-2 fw-semibold" id="btnSimpanRentPendinginSurjal">
                                    <i class='bx bx-save me-1'></i>SIMPAN
                                </button>
                            </div>

                            <div class="col-md-3 col-sm-6 d-none" id="btnMuatRentPendinginDiv">
                                <button class="btn btn-primary btn-sm w-100 py-2 fw-semibold" id="btnMuatRentPendingin">
                                    <i class='bx bx-car me-1'></i>PROSES NOMUAT
                                </button>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <button class="btn btn-danger btn-sm w-100 py-2 fw-semibold" id="btnClearRentPendinginSurjal">
                                    <i class='bx bx-trash me-1'></i>CLEAR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Status Deteksi -->
            {{-- <div class="mt-3 text-end">
                <span class="status-deteksi">STATUS: DATA DITEMUKAN - NO. MUAT: MU20260000023</span>
            </div> --}}
        </div>
    </div>
</div>
{{-- MODAL --}}
@include('rentPendingin.rentPendingin_modal')
<script>
const userRole = "{{ auth()->user()->roles }}";
const userId = "{{ auth()->user()->user_id }}";
$(document).ready(function() {
    // Set CSRF token in AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log(userRole);
    // Clear Form
    clearRentDinginForm();
    // ================================= Pilih No Muat =====================================
    $('#no_muat_rent_dingin_btn').click(function(e) {
        e.preventDefault();
        $('#muatModalRentDgn').modal('show');
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalMuatRentDgnTable')) {
            $('#modalMuatRentDgnTable').DataTable().destroy();
        }
        var tableMuat = $('#modalMuatRentDgnTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url: "{{ route('rentPendingin.data') }}",
                data: function(d) {
                    d.tgl_mulai = $('#filter_tgl_mulai_rentdgn').val();
                    d.tgl_akhir = $('#filter_tgl_akhir_rentdgn').val();
                    d.search_muat = $('#filter_rent_dgn').val();
                },
                dataSrc: function(response) {
        // Debug: lihat struktur data di console
        console.log('Response Data:', response.data);
            return response.data;
        }
            },
            // Scroll settings
            scrollX: true,
            scrollY: "400px",
            scrollCollapse: true,
            // Responsive settings
            responsive: true,
            autoWidth: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'NOMUAT', name: 'NOMUAT' },
                { data: 'TGLMUAT', name: 'TGLMUAT' },
                { data: 'CUSTOMER', name: 'CUSTOMER' },
                { data: 'rute', name: 'rute' },
                { data: 'JUMLAH', name: 'JUMLAH' },
                { data: 'harga_formatted', name: 'HARGA' },
                { data: 'DISC', name: 'DISC' },
                { data: 'dc_formatted', name: 'DC' },
                { data: 'total_formatted', name: 'GRAND' },
                { data: 'NOSJ', name: 'NOSJ' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn_filter_rent_dgn').click(function () {
            tableMuat.ajax.reload();
        });
    });
    // ============================= End Of Pilih No Muat =====================================
    // ================================ Delete No Rent Pendingin ======================================
    $(document).on('click', '.deleteRentDgn', function () {
        let id     = $(this).data('id');
        let nomuat = $(this).data('nomuat');

        Swal.fire({
            title: 'Hapus Data?',
            text: 'No Muat ' + nomuat + ' akan dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('rentPendingin.destroy', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // reload datatable
                        $('#modalMuatRentDgnTable').DataTable().ajax.reload(null, false);
                    },
                    error: function () {
                        Swal.fire(
                            'Gagal!',
                            'Data tidak bisa dihapus',
                            'error'
                        );
                    }
                });
            }
        });
    });
    // ========================= End Of Delete No Rent Pendingin =================================
    // ================================= Pilih Customer =====================================
    $('#customer_rent_dingin_btn').click(function(e) {
        e.preventDefault();
        $('#customerModalDgn').modal('show');
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalCusDgnTable')) {
            $('#modalCusDgnTable').DataTable().destroy();
        }
        var table = $('#modalCusDgnTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("rentPendingin-cus.data") }}',
            // Scroll settings
            scrollX: true,
            scrollY: "400px",
            scrollCollapse: true,
            // Responsive settings
            responsive: true,
            autoWidth: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode_cus', name: 'kode_cus' },
                { data: 'NAMACUST', name: 'NAMACUST' },
                { data: 'TYPECUST', name: 'TYPECUST' },
                { data: 'TELEPON', name: 'TELEPON' },
                { data: 'EMAIL', name: 'EMAIL' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    // ### Select Button
    $(document).on('click', '.view-btn-customer-rent-dingin', function(e) {
        e.preventDefault();
        var kodeCus = $(this).data('id');
        var namaCus = $(this).data('name');
        var CUSTOMER = $(this).data('customer');
        var alamat = $(this).data('alamat');
        var telepon = $(this).data('telepon');
        // Mengisi nilai ke elemen yang dituju
        $('#customer_rent_dingin_id').val(kodeCus);
        $('#customer_rent_dingin').val(namaCus);
        $('#customer_kode_dingin').val(CUSTOMER);
        // Kosongkan dulu item
        $('#alamat_rent_dingin').val(alamat);
        $('#telpon_rent_dingin').val(telepon);
        $('#nama_penerima_rent_dingin').val(namaCus);

        // Tutup modal
        $('#customerModalDgn').modal('hide');
    });
    // ============================== End Of Pilih Customer ==================================
    // =================================== Pilih Item =====================================
    $(document).on('click', '#item_rent_dingin_btn', function(e) {
        var expedisiId = $('#customer_rent_dingin_id').val();

        if (!expedisiId || expedisiId.trim() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Silahkan Pilih Customer!',
                confirmButtonColor: '#3085d6'
            });
            e.preventDefault();
            return false;
        }

        $('#itemModalDgn').modal('show');

        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalItemDgnTable')) {
            $('#modalItemDgnTable').DataTable().destroy();
        }

        // rebuild datatable
        $('#modalItemDgnTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('price-rentcus-modal.price', ':kode') }}".replace(':kode', expedisiId),
                dataSrc: function (json) {
                    // SET INFO CUSTOMER DI ATAS TABEL
                    $("#custNameDgn").text(json.customer_nama);
                    $("#custKodeDgn").text(json.customer_kode);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false },
                { data: 'nama_kendaraan' },
                { data: 'ITEM' },
                { data: 'PERIODE' },
                { data: 'PLAT' },
                { data: 'JENIS' },
                { data: 'harga_html', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    });
    // ### Select Button
    $(document).on('click', '.pick-price-dgn', function(e) {
        e.preventDefault();
        var kodeCus = $(this).data('id');
        var kodeMbl = $(this).data('kodembl');
        var kodeDgn = $(this).data('kode');
        // Ambil KETERANGAN dari kolom di baris yang sama
        var row = $(this).closest('tr');
        var kendaraan = row.find('td:eq(1)').text();
        var item = row.find('td:eq(2)').text();
        var harga = row.find('td:eq(6)').text().trim();

        // Mengisi nilai ke elemen yang dituju
        $('#jml_hari_rent_dingin').val(1);
        $('#item_rent_dingin_id').val(kodeDgn);
        $('#item_rent_dingin').val(item);
        $('#kendaraan_rent_dingin_id').val(kodeMbl);
        $('#kendaraan_rent_dingin').val(kendaraan);
        $('#harga_rent_dingin').val(harga);
        calculateTotalDgn();
        // Tutup modal
        $('#itemModalDgn').modal('hide');
    });
    // =============================== End Of Pilih Item ==================================
    // =================================== Pilih Driver =====================================
    $(document).on('click', '#driver_rent_dingin_btn', function(e) {
        e.preventDefault();
        var kodeKen = $(this).data('id');
        $('#driverModalDgn').modal('show');

        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalDriverDgnTable')) {
            $('#modalDriverDgnTable').DataTable().destroy();
        }

        // rebuild datatable
        $('#modalDriverDgnTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('driver-modal.data') }}",
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'KODE', name: 'KODE'},
                {data: 'NAMA', name: 'NAMA'},
                {data: 'ALAMAT', name: 'ALAMAT',
                    render: function(data) {
                        return data && data.length > 30 ? data.substr(0, 30) + '...' : data;
                    }
                },
                {data: 'PHONE', name: 'PHONE'},
                {data: 'MULAI', name: 'MULAI',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('id-ID') : '-';
                    }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
        });
    });
    // ### Select Button
    $(document).on('click', '.pickDriverModal', function(e) {
        e.preventDefault();
        var kodeDriver = $(this).data('id');
        // Ambil KETERANGAN dari kolom di baris yang sama
        var row = $(this).closest('tr');
        var nama = row.find('td:eq(2)').text();
        $('#driver_rent_dingin_id').val(kodeDriver);
        $('#driver_rent_dingin').val(nama);
        // Tutup modal
        $('#driverModalDgn').modal('hide');
    });
    // =============================== End Of Pilih Driver ==================================
    // =================================== Pilih Kendaraan =====================================
    $(document).on('click', '#kendaraan_rent_dingin_btn', function(e) {

        $('#kendaraanModalDgn').modal('show');

        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalKendaraanDgnTable')) {
            $('#modalKendaraanDgnTable').DataTable().destroy();
        }

        // rebuild datatable
        $('#modalKendaraanDgnTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('kendaraan.datamodel') }}",
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'KODE', name: 'KODE'},
                {data: 'NAMA', name: 'NAMA'},
                {data: 'PLAT', name: 'PLAT'},
                {data: 'JENIS', name: 'JENIS'},
                {data: 'FNO_PRK_B', name: 'FNO_PRK_B'},
                {data: 'FNO_PRK_P', name: 'FNO_PRK_P'},
                {data: 'FNO_PRK_S', name: 'FNO_PRK_S'},
                {data: 'FNO_PRK_O', name: 'FNO_PRK_O'},
                {data: 'FNO_PRK_M', name: 'FNO_PRK_M'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    });
    // ### Select Button
    $(document).on('click', '.pickKendaraanModel', function(e) {
        e.preventDefault();
        var kodeKendaraan = $(this).data('id');
        // Ambil KETERANGAN dari kolom di baris yang sama
        var row = $(this).closest('tr');
        var keterangan = row.find('td:eq(1)').text();
        var nama = row.find('td:eq(2)').text();

        // Mengisi nilai ke elemen yang dituju
        $('#kendaraan_rent_dingin_id').val(keterangan);
        $('#kendaraan_rent_dingin').val(nama);

        // Tutup modal
        $('#kendaraanModalDgn').modal('hide');
    });
    // =============================== End Of Pilih Kendaraan ==================================
    // =================================== Input Total ======================================
    $('#harga_rent_dingin, #jml_hari_rent_dingin, #discount_rent_dingin').on('input', function() {
        calculateTotalDgn();
    });
    // =============================== End Of Input Total ==================================
    // =============================== Submit Form Surjal ==================================
    $('#btnSimpanRentPendinginSurjal').on('click', function () {
        let formData = {
            _token: $('input[name="_token"]').val(),

            no_muat_rent_dingin: $('#no_muat_rent_dingin').val(),
            tanggal_rent_dingin: $('#tanggal_rent_dingin').val(),

            no_surjal_rent_dingin: $('#no_surjal_rent_dingin').val(),
            tanggal_surjal_rent_dingin: $('#tanggal_surjal_rent_dingin').val(),
            wilayah_nosj_rent_dingin: $('#wilayah_nosj_rent_dingin').val(),

            customer_rent_dingin_id: $('#customer_rent_dingin_id').val(),
            customer_rent_dingin: $('#customer_rent_dingin').val(),
            nama_penerima_rent_dingin: $('#nama_penerima_rent_dingin').val(),
            telpon_rent_dingin: $('#telpon_rent_dingin').val(),
            alamat_rent_dingin: $('#alamat_rent_dingin').val(),

            item_rent_dingin: $('#item_rent_dingin').val(),

            jml_hari_rent_dingin: $('#jml_hari_rent_dingin').val(),
            harga_rent_dingin: $('#harga_rent_dingin').val(),
            discount_rent_dingin: $('#discount_rent_dingin').val(),
            pajak_rent_dingin: $('#pajak_rent_dingin').val(),
            total_rent_dingin: $('#total_rent_dingin').val(),

            driver_rent_dingin_id: $('#driver_rent_dingin_id').val(),
            driver_rent_dingin: $('#driver_rent_dingin').val(),

            kendaraan_rent_dingin_id: $('#kendaraan_rent_dingin_id').val(),
            kendaraan_rent_dingin: $('#kendaraan_rent_dingin').val(),

            keterangan_rent_dingin: $('#keterangan_rent_dingin').val(),
            KETERANGAN: 'REN ' + ($('#item_rent_dingin').val() || '') + ' ' + ($('#customer_rent_dingin').val() || '') + ' KE ' + ($('#nama_penerima_rent_dingin').val() || ''),
        };
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            // Jalankan setelah modal benar2 muncul
            submitFormSurjalRentDgn();
        });
        function submitFormSurjalRentDgn(){

            var url = $('#no_surjal_rent_dingin').val() ? "{{ route('rentPendinginSurjal.update', ':nosj') }}".replace(':nosj', $('#no_surjal_rent_dingin').val()): "{{ route('rentPendingin-surjal.store') }}";

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (res) {
                    if (res.success) {
                        $('#loading_modal').modal('hide');
                        if($('#no_surjal_rent_dingin').val() != ""){
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Diupdate!',
                                text: `Data berhasil diupdate dengan NOSJ: ${res.data.NOSJ}`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#2add69'
                            });
                        }else{
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disimpan!',
                                text: `Data berhasil disimpan dengan NOSJ: ${res.data.NOSJ}`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }

                        clearRentDinginForm();
                        setButtonToSaveMode();
                        // reset form kalau mau
                        // $('#formRentPendingin')[0].reset();
                    }
                },

                error: function (xhr) {
                    $('#loading_modal').modal('hide');
                    let msg = 'Terjadi kesalahan';

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        msg = Object.values(errors).map(e => e[0]).join('\n');
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    alert(msg);
                },
            });
        }
    });
    // ============================ End Of Submit Form Surjal ==================================
    // =============================== Clear Form Surjal ==================================
    $('#btnClearRentPendinginSurjal').on('click', function () {
        clearRentDinginForm();
        setButtonToSaveMode();
    });
    // ============================ End Of Clear Form Surjal ================================
    // ================================= Pilih No Surat Jalan =====================================
    $('#no_surjal_rent_dingin_btn').click(function(e) {
        e.preventDefault();
        $('#surjalModalRentPendingin').modal('show');
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalSurjalRentDgnTable')) {
            $('#modalSurjalRentDgnTable').DataTable().destroy();
        }
        var tableSurjalRentDgn = $('#modalSurjalRentDgnTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url: "{{ route('rentPendingin-surjal.data') }}",
                data: function(d) {
                    d.tgl_mulai = $('#filter_tgl_mulai').val();
                    d.tgl_akhir = $('#filter_tgl_akhir').val();
                    d.search_muat = $('#filter_surjal_rent_dgn').val();
                }
            },
            // Scroll settings
            scrollX: true,
            scrollY: "400px",
            scrollCollapse: true,
            // Responsive settings
            responsive: true,
            autoWidth: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'NOSJ', name: 'NOSJ' },
                { data: 'tglsj', name: 'tglsj' },
                { data: 'CUSTOMER', name: 'CUSTOMER' },
                { data: 'PESANAN', name: 'PESANAN' },
                { data: 'JUMLAH', name: 'JUMLAH' },
                { data: 'harga_formatted', name: 'HARGA' },
                { data: 'DISC', name: 'DISC' },
                { data: 'total_formatted', name: 'GRAND' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn_filter_surjal_rent_dgn').click(function () {
            tableSurjalRentDgn.ajax.reload();
        });
    });
    // ============================= End Of Pilih No Surat Jalan =====================================
    // =============================== Show Form Surjal ==================================
    $(document).on('click', '.pickSurjalRentDgn', function (e) {
        e.preventDefault();
        const nosj = $(this).data('nosj');

        if (!nosj) {
            alert('No Surat Jalan tidak ditemukan');
            return;
        }

        const surjalDataUrlRentDingin = "{{ route('rentPendinginSurjal.show', ['nosj' => ':nosj']) }}";
        const url = surjalDataUrlRentDingin.replace(':nosj', nosj);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                // optional loading
                $('#loadingModal').modal('show');
            },
            success: function (res) {
                if (!res.success) {
                    alert(res.message || 'Data tidak ditemukan');
                    return;
                }
                $('#surjalModalRentPendingin').modal('hide');
                const d = res.data;

                // =========================
                // ISI FORM
                // =========================
                $('#no_surjal_rent_dingin').val(d.NOSJ);
                $('#tanggal_surjal_rent_dingin').val(d.tglsj);
                $('#wilayah_nosj_rent_dingin').val(d.WILAYAH);

                // CUSTOMER
                $('#customer_rent_dingin_id').val(d.CUSTOMER_KODE);
                $('#customer_rent_dingin').val(d.CUSTOMER);
                $('#customer_kode_dingin').val(d.CUSTOMER_KODE);
                $('#nama_penerima_rent_dingin').val(d.P_NAMA ?? '');
                $('#telpon_rent_dingin').val(d.P_PHONE ?? '');
                $('#alamat_rent_dingin').val(d.P_ALAMAT ?? '');

                // ITEM
                $('#item_rent_dingin').val(d.PESANAN);
                $('#item_rent_dingin_id').val(d.PESANAN);

                // DRIVER & KENDARAAN
                $('#driver_rent_dingin_id').val(d.DRIVER);
                $('#driver_rent_dingin').val(d.NAMA_DRIVER);
                $('#kendaraan_rent_dingin_id').val(d.KENDARAAN);
                $('#kendaraan_rent_dingin').val(d.NAMA_KENDARAAN);

                // PERHITUNGAN
                $('#jml_hari_rent_dingin').val(d.JUMLAH);
                $('#harga_rent_dingin').val(formatRupiah(d.HARGA));
                $('#discount_rent_dingin').val(d.DISC); // persen
                $('#sub_total_rent_dingin').val(formatRupiah(d.JUMLAH * d.HARGA));
                $('#dpp_rent_dingin').val(formatRupiah(d.TOTAL));
                $('#pajak_rent_dingin').val(d.PPN);
                $('#total_rent_dingin').val(formatRupiah(d.GRAND));

                $('#keterangan_rent_dingin').val(d.catatan);

                // Hitung ulang biar konsisten
                calculateTotalDgn();
                setButtonToUpdateMode();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('Gagal mengambil data');
            },
            complete: function () {
                $('#loadingModal').modal('hide');
            }
        });
    });
    // ============================ End Of Show Form Surjal ==================================
    // ================================ Delete No Surat Jalan ======================================
    $(document).on('click', '.deleteSurjalRentDgn', function () {
        let id     = $(this).data('id');
        let nosj = $(this).data('nosj');

        Swal.fire({
            title: 'Hapus Data?',
            text: 'No Muat ' + nosj + ' akan dihapus!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = "{{ route('rentPendinginSurjal.destroy', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // reload datatable
                        $('#modalSurjalRentDgnTable').DataTable().ajax.reload(null, false);
                    },
                    error: function () {
                        Swal.fire(
                            'Gagal!',
                            'Data tidak bisa dihapus',
                            'error'
                        );
                    }
                });
            }
        });
    });
    // ============================ End Of Delete No Surat Jalan ====================================
    // =============================== Submit Form Muat ==================================
    $('#btnMuatRentPendingin').on('click', function () {
        let formData = {
            _token: $('input[name="_token"]').val(),

            no_muat_rent_dingin: $('#no_muat_rent_dingin').val(),
            tanggal_rent_dingin: $('#tanggal_rent_dingin').val(),

            no_surjal_rent_dingin: $('#no_surjal_rent_dingin').val(),
            tanggal_surjal_rent_dingin: $('#tanggal_surjal_rent_dingin').val(),
            wilayah_nosj_rent_dingin: $('#wilayah_nosj_rent_dingin').val(),

            customer_rent_dingin_id: $('#customer_rent_dingin_id').val(),
            customer_rent_dingin: $('#customer_rent_dingin').val(),
            nama_penerima_rent_dingin: $('#nama_penerima_rent_dingin').val(),
            telpon_rent_dingin: $('#telpon_rent_dingin').val(),
            alamat_rent_dingin: $('#alamat_rent_dingin').val(),

            item_rent_dingin: $('#item_rent_dingin').val(),

            jml_hari_rent_dingin: $('#jml_hari_rent_dingin').val(),
            harga_rent_dingin: $('#harga_rent_dingin').val(),
            discount_rent_dingin: $('#discount_rent_dingin').val(),
            pajak_rent_dingin: $('#pajak_rent_dingin').val(),
            total_rent_dingin: $('#total_rent_dingin').val(),

            driver_rent_dingin_id: $('#driver_rent_dingin_id').val(),
            driver_rent_dingin: $('#driver_rent_dingin').val(),

            kendaraan_rent_dingin_id: $('#kendaraan_rent_dingin_id').val(),
            kendaraan_rent_dingin: $('#kendaraan_rent_dingin').val(),

            keterangan_rent_dingin: $('#keterangan_rent_dingin').val(),
            KETERANGAN: 'REN ' + ($('#item_rent_dingin').val() || '') + ' ' + ($('#customer_rent_dingin').val() || '') + ' KE ' + ($('#nama_penerima_rent_dingin').val() || ''),
        };
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            // Jalankan setelah modal benar2 muncul
            submitFormMuatRentDgn();
        });
        function submitFormMuatRentDgn(){
            let nosj = $('#no_surjal_rent_dingin').val();
            let url = "{{ route('rentPendinginMuat.update', ':nosj') }}";
            url = url.replace(':nosj', nosj);

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (res) {
                    if (res.success) {
                        $('#loading_modal').modal('hide');
                        if($('#no_surjal_rent_dingin').val() != ""){
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Diupdate!',
                                text: `Data berhasil diupdate dengan NOSJ: ${res.data.NOSJ}`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#2add69'
                            });
                        }else{
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Disimpan!',
                                text: `Data berhasil disimpan dengan NOSJ: ${res.data.NOSJ}`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3085d6'
                            });
                        }

                        clearRentDinginForm();
                        setButtonToSaveMode();
                        // reset form kalau mau
                        // $('#formRentPendingin')[0].reset();
                    }
                },

                error: function (xhr) {
                    $('#loading_modal').modal('hide');
                    let msg = 'Terjadi kesalahan';

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        msg = Object.values(errors).map(e => e[0]).join('\n');
                    } else if (xhr.responseJSON?.message) {
                        msg = xhr.responseJSON.message;
                    }

                    alert(msg);
                },
            });
        }
    });
    // ============================ End Of Submit Form Muat ==================================
});
    // ########################################################################
    // FUNCTION HELPER:
    // ########################################################################
    // =================== Pajak PPN ==========================
    function loadInputPajakDgn(){
        $.ajax({
            url: '{{ route('get_pajak') }}',
            type: 'GET',
            success: function(response) {
                let nilai_ppn = response.data.ppn;
                $('#pajak_rent_dingin').val(nilai_ppn);
            },
            error: function() {
                $('#ppn_expedisi').val('Error Loading');
            }
        });
    }

    // =================== Hitung Total Form ==========================
    function unformatRupiah(value) {
        if (!value) return 0;
        return parseInt(value.replace(/[^\d]/g, '')) || 0;
    }

    function formatRupiah(value) {
        return value.toLocaleString('id-ID');
    }

    function calculateTotalDgn() {
        // =============================
        // AMBIL NILAI DARI FORM
        // =============================
        const harga = unformatRupiah($('#harga_rent_dingin').val()) || 0;
        const hari  = parseInt($('#jml_hari_rent_dingin').val()) || 0;

        // discount = PERSEN (misal 10)
        const discountPercent = parseFloat($('#discount_rent_dingin').val()) || 0;

        // pajak = PERSEN (misal 11)
        const pajakPercent = parseFloat($('#pajak_rent_dingin').val()) || 0;

        // =============================
        // PERHITUNGAN
        // =============================

        // 1. Sub Total
        const subTotal = harga * hari;

        // 2. Discount dalam RUPIAH
        const discountAmount = subTotal * (discountPercent / 100);

        // 3. DPP (setelah discount)
        const dpp = subTotal - discountAmount;

        // 4. Pajak
        const pajakAmount = dpp * (pajakPercent / 100);

        // 5. Total
        const total = dpp + pajakAmount;

        // =============================
        // SET KE FORM
        // =============================
        $('#sub_total_rent_dingin').val(formatRupiah(Math.round(subTotal)));
        $('#dpp_rent_dingin').val(formatRupiah(Math.round(dpp)));
        $('#total_rent_dingin').val(formatRupiah(Math.round(total)));
    }

    function clearRentDinginForm() {
        // Reset No. Muat dan Tanggal
        $('#no_muat_rent_dingin').val('');
        $('#tanggal_rent_dingin').val(new Date().toISOString().split('T')[0]);
        // Reset No. Surat Jalan dan wilayah
        $('#no_surjal_rent_dingin').val('');
        $('#tanggal_surjal_rent_dingin').val(new Date().toISOString().split('T')[0]);
        $('#wilayah_nosj_rent_dingin').val('DENPASAR'); // Reset ke default

        // Reset Customer Data
        $('#customer_rent_dingin_id').val('');
        $('#customer_rent_dingin').val('');
        $('#customer_kode_dingin').val('');
        $('#nama_penerima_rent_dingin').val('');
        $('#telpon_rent_dingin').val('');
        $('#alamat_rent_dingin').val('');

        // Reset Item
        $('#item_rent_dingin_id').val('');
        $('#item_rent_dingin').val('');
        $('#jml_hari_rent_dingin').val('');
        $('#harga_rent_dingin').val('');

        // Reset Driver dan Kendaraan
        $('#driver_rent_dingin_id').val('');
        $('#driver_rent_dingin').val('');
        $('#kendaraan_rent_dingin_id').val('');
        $('#kendaraan_rent_dingin').val('');

        // Reset Perhitungan
        $('#sub_total_rent_dingin').val('0');
        $('#discount_rent_dingin').val('0');
        $('#dpp_rent_dingin').val('0');
        loadInputPajakDgn();
        $('#keterangan_rent_dingin').val('');
        $('#total_rent_dingin').val('0');

        // Focus ke field pertama
        // $('#no_muat_rent_dingin').focus();
    }

    function setButtonToUpdateMode() {
        $('#btnSimpanRentPendinginSurjal')
            .removeClass('btn-info')
            .addClass('btn-success')
            .html('<i class="bx bx-edit me-1"></i>UPDATE')
            .attr('title', 'Update data ekspedisi')
            .data('mode', 'update')

        // Tambahkan badge info
        $('#btnSimpanRentPendinginSurjal').append('<span class="badge bg-light text-dark ms-2">EDIT MODE</span>');
        // aktifkan button papend to table
        if (userRole === 'admin') {
            $('#btnMuatRentPendinginDiv').removeClass('d-none');
        }
    }

    // Fungsi untuk mengubah tombol ke mode CREATE/SIMPAN
    function setButtonToSaveMode() {
        $('#btnSimpanRentPendinginSurjal')
            .removeClass('btn-success')
            .addClass('btn-info')
            .html('<i class="bx bx-save me-1"></i>SIMPAN')
            .attr('title', 'Simpan data ekspedisi baru')
            .data('mode', 'save')
            .data('id', '')
            .find('.badge').remove(); // Hapus badge jika ada
            // $('#divPrintSuratJalan').addClass('d-none');
            // aktifkan button papend to table
            $('#btnMuatRentPendinginDiv').addClass('d-none');
    }
</script>

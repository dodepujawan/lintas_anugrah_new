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
</style>
<div class="container-fluid mt-3">
    <!-- Header Form -->
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-truck me-2'></i>FORM EXPEDISI</h5>
        </div>

    {{-- HEADER DOKUMEN --}}
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-file'></i> DATA DOKUMEN</h5>
            <span class="badge bg-success ms-2">READY</span>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">TGL SJ</label>
                <input type="date" class="form-control form-control-sm" id="tgl_sj_expedisi" name="tgl_sj_expedisi">
            </div>
            <div class="col-md-4">
                <label class="form-label">NO SJ</label>
                <div class="input-group input-group-sm mb-2">
                    <input type="text" class="form-control form-control-sm" id="no_sj_expedisi" name="no_sj_expedisi" placeholder="Auto Generate/ Click To Update" Readonly>
                    <button class="btn btn-outline-secondary" id="no_sj_expedisi_btn"><i class="bx bx-search"></i></button>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">WILAYAH</label>
                <select class="form-select form-select-sm" id="wilayah_expedisi" name="wilayah_expedisi">
                    <option value="denpasar">DENPASAR</option>
                    <option value="gianyar">GIANYAR</option>
                </select>
            </div>
            {{-- <div class="col-md-3">
                <label class="form-label">NOMOR PERJALANAN</label>
                <input type="text" class="form-control form-control-sm" id="nomor_perjalanan_expedisi" name="nomor_perjalanan_expedisi" placeholder="Auto Generate" readonly>
            </div> --}}
        </div>
        <div class="row">
            <!-- CUSTOMER SECTION -->
            <div class="col-md-6 mt-2">
                <div class="card border-light">
                    <div class="card-body p-2">
                        <label class="form-label fw-semibold">CUSTOMER</label>
                        <div class="input-group input-group-sm mb-2">
                            <input type="hidden" name="customer_expedisi_id" id="customer_expedisi_id">
                            <input type="text" class="form-control" id="customer_expedisi"
                                name="customer_expedisi" readonly
                                placeholder="Pilih customer...">
                            <button class="btn btn-outline-primary" id="customer_expedisi_btn" type="button">
                                <i class="bx bx-search"></i>
                            </button>
                        </div>

                        <!-- CUSTOMER KODE -->
                        <div class="customer-kode-info">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark me-2">Kode:</span>
                                <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                                    id="customer_kode_expedisi" name="customer_kode_expedisi"
                                    readonly style="font-weight: 600;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <label class="form-label">ITEM</label>
                <div class="input-group input-group-sm">
                    <input type="hidden" name="item_expedisi_id" id="item_expedisi_id">
                    <input type="hidden" name="item_expedisi_tipe" id="item_expedisi_tipe">
                    <input type="text" class="form-control" id="item_expedisi" name="item_expedisi" readonly placeholder="Pilih item...">
                    <button class="btn btn-outline-secondary" id="item_expedisi_btn"><i class="bx bx-search"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- KENDARAAN & DRIVER --}}
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-truck'></i> KENDARAAN & DRIVER</h5>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">KENDARAAN</label>
                <div class="input-group input-group-sm">
                    <input type="hidden" class="form-control" id="kendaraan_expedisi_id" name="kendaraan_expedisi_id" placeholder="Pilih kendaraan...">
                    <input type="text" class="form-control" id="kendaraan_expedisi" name="kendaraan_expedisi" placeholder="Pilih kendaraan..." readonly>
                    <button class="btn btn-outline-secondary" id="kendaraan_expedisi_btn"><i class="bx bx-search"></i></button>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">DRIVER I</label>
                <div class="input-group input-group-sm">
                    <input type="hidden" class="form-control" id="driver_1_expedisi_id" name="driver_1_expedisi_id">
                    <input type="text" class="form-control" id="driver_1_expedisi" name="driver_1_expedisi" readonly placeholder="Pilih drver I">
                    <button class="btn btn-outline-secondary" id="driver_1_expedisi_btn" data-id="1"><i class="bx bx-search"></i></button>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">DRIVER II</label>
                <div class="input-group input-group-sm">
                    <input type="hidden" class="form-control" id="driver_2_expedisi_id" name="driver_2_expedisi_id">
                    <input type="text" class="form-control" id="driver_2_expedisi" name="driver_2_expedisi" readonly placeholder="Pilih driver II">
                    <button class="btn btn-outline-secondary" id="driver_2_expedisi_btn" data-id="2"><i class="bx bx-search"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA PENERIMA --}}
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-user'></i> DATA PENERIMA</h5>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">PENERIMA</label>
                <input type="text" class="form-control form-control-sm" id="penerima_expedisi" name="penerima_expedisi">
            </div>
            <div class="col-md-4">
                <label class="form-label">NAMA</label>
                <input type="text" class="form-control form-control-sm" id="nama_penerima_expedisi" name="nama_penerima_expedisi">
            </div>
            <div class="col-md-4">
                <label class="form-label">PHONE</label>
                <input type="text" class="form-control form-control-sm" id="phone_penerima_expedisi" name="phone_penerima_expedisi">
            </div>
            <div class="col-md-12 mt-2">
                <label class="form-label">ALAMAT</label>
                <textarea class="form-control form-control-sm" rows="2" id="alamat_penerima_expedisi" name="alamat_penerima_expedisi"></textarea>
            </div>
        </div>
    </div>

    {{-- DETAIL PENGIRIMAN & PERHITUNGAN --}}
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-calculator'></i> DETAIL & PERHITUNGAN</h5>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">RUTE</label>
                <input type="text" class="form-control form-control-sm" id="rute_expedisi" name="rute_expedisi">
                {{-- <select class="form-select form-select-sm" id="rute_expedisi" name="rute_expedisi"></select> --}}
            </div>
            <div class="col-md-2">
                <label class="form-label">JUMLAH</label>
                <input type="number" class="form-control form-control-sm" id="jumlah_expedisi" name="jumlah_expedisi">
            </div>
            <div class="col-md-2">
                <label class="form-label">HARGA @</label>
                <input type="text" class="form-control form-control-sm" id="harga_expedisi" name="harga_expedisi">
            </div>
            <div class="col-md-2">
                <label class="form-label">DISC %</label>
                <input type="number" class="form-control form-control-sm" id="disc_expedisi" name="disc_expedisi">
            </div>
            <div class="col-md-3">
                <label class="form-label">DEL CHARGE</label>
                <div class="input-group input-group-sm">
                    <input type="number" class="form-control" id="del_charge_expedisi" name="del_charge_expedisi">
                    <button class="btn btn-warning" id="auto_dc_expedisi" name="auto_dc_expedisi">Auto DC</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">SUB TOTAL</label>
                <input type="text" class="form-control form-control-sm" id="sub_total_expedisi" name="sub_total_expedisi" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">DPP</label>
                <input type="text" class="form-control form-control-sm" id="dpp_expedisi" name="dpp_expedisi" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">PPN</label>
                <input type="text" class="form-control form-control-sm" id="ppn_expedisi" name="ppn_expedisi" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">GRAND TOTAL</label>
                <input type="text" class="form-control form-control-sm" id="grand_total_expedisi" name="grand_total_expedisi" readonly>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card-expedisi">
        <div class="row g-2">
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-info btn-action w-100" id="buttonSimpanExp">
                    <i class='bx bx-save me-1'></i>SIMPAN [F3]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-danger btn-action w-100" id="buttonClearExp">
                    <i class='bx bx-trash me-1'></i>Clear [F6]
                </button>
            </div>
            <div class="col-md-3 col-sm-6 d-none" id="divPrintSuratJalan">
                <button class="btn btn-primary" id="btnPrintSuratJalan" data-id="">
                    <i class="bx bx-printer"></i> Print Surat Jalan
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Data SJ -->
    <div class="card-expedisi tabel-surat-jalan">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">TGL MUAT</label>
                <input type="date" class="form-control form-control-sm" id="tgl_muat_expedisi" name="tgl_muat_expedisi" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">NO MUAT</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="no_muat_expedisi" name="no_muat_expedisi" readonly placeholder="Auto Generate/ click for update">
                    <button class="btn btn-outline-secondary" type="button" id="muat_expedisi_btn">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-expedisi-header mt-3">
            <h5><i class='bx bx-table me-2'></i>DATA SURAT JALAN</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover" id="tableProsesExpedisi">
                <thead>
                    <tr>
                        <th width="30">NO</th>
                        <th width="120">NO.SJ</th>
                        <th width="100">TGL.SJ</th>
                        <th width="100">DELCHARGE</th>
                        <th width="80">JUMLAH</th>
                        <th width="80">UNIT</th>
                        <th width="80">JENIS</th>
                        <th width="100">HARGA</th>
                        <th width="100">DISC</th>
                        <th width="100">PPN</th>
                        <th width="100">TOTAL</th>
                        <th width="80">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10" class="text-end">GRAND TOTAL</th>
                        <th class="text-end" id="grandTotal">0</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-md-3 col-sm-6 d-flex gap-2">
            <button class="btn btn-success btn-action flex-fill" id="simpanMuatExpBtn" style="display:none">
                <i class='bx bx-plus-circle me-1'></i>Simpan No Muat
            </button>
            <button class="btn btn-danger btn-action flex-fill" id="clearMuatExpBtn" style="display:none">
                <i class='bx bx-trash me-1'></i>Clear No Muat
            </button>
        </div>
    </div>
</div>
@include('expedisi.expedisi_modal')
<script>
$(document).ready(function() {
    // Set CSRF token in AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    resetFormExpedisi();
    // ================================= driver Login =====================================
    const userRole = "{{ auth()->user()->roles }}";
    const userId = "{{ auth()->user()->user_id }}";
    // console.log(userId);
    if (userRole === 'driver') {
        const driverDataUrlTemplate = "{{ route('driver-det.data', ['user_id' => ':user_id']) }}";
        function getMyKodeDriver(userId) {
            const url = driverDataUrlTemplate.replace(':user_id', userId);
            return $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json'
            });
        }
        getMyKodeDriver(userId)
            .done(function (res) {
                if (res.success) {
                    $('#driver_1_expedisi_id').val(res.kode);
                    $('#driver_1_expedisi').val(res.nama);
                    $('#driver_1_expedisi_btn')
                    .prop('disabled', true)
                    .addClass('disabled');

                    $('#jumlah_expedisi').prop('readonly', true);
                    $('#harga_expedisi').prop('readonly', true);
                    $('#disc_expedisi').prop('readonly', true);
                    $('#del_charge_expedisi').prop('readonly', true);
                    $('#auto_dc_expedisi').prop('disabled', true).addClass('disabled');
                    $('#jumlah_expedisi').val(1);
                    $('.tabel-surat-jalan').hide();
                }
            })
            .fail(function (xhr) {
                console.error(xhr.responseText);
                alert('Data driver tidak ditemukan');
            });
    }
    // ============================ End Of driver Login ===================================
    // ================================= Pilih No Surat Jalan =====================================
    $('#no_sj_expedisi_btn').click(function(e) {
        e.preventDefault();
        $('#surjalModalExp').modal('show');
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalSurjalExpTable')) {
            $('#modalSurjalExpTable').DataTable().destroy();
        }
        var tableSurjal = $('#modalSurjalExpTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url: "{{ route('expedisi-surjal.data') }}",
                data: function(d) {
                    d.tgl_mulai = $('#filter_tgl_mulai').val();
                    d.tgl_akhir = $('#filter_tgl_akhir').val();
                    d.search_muat = $('#filter_surjal').val();
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
                { data: 'rute', name: 'rute' },
                { data: 'JUMLAH', name: 'JUMLAH' },
                { data: 'harga_formatted', name: 'HARGA' },
                { data: 'DISC', name: 'DISC' },
                { data: 'dc_formatted', name: 'DC' },
                { data: 'total_formatted', name: 'GRAND' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#btn_filter_surjal').click(function () {
            tableSurjal.ajax.reload();
        });
    });
    // ============================= End Of Pilih No Surat Jalan =====================================
    // ================================ Delete No Surat Jalan ======================================
    $(document).on('click', '.deleteSurjal', function () {
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
                let url = "{{ route('expedisi.destroy', ':id') }}";
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
                        $('#modalSurjalExpTable').DataTable().ajax.reload(null, false);
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
    // ============================= Show Detail Surjal ================================
    // Event listener untuk tombol pickMuat
    $(document).on('click', '.pickSurjal', function() {
        const id = $(this).data('id');
        const nomuat = $(this).data('nosj');

        $('#surjalModalExp').modal('hide');
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            // Ambil data dari server berdasarkan id/nomuat
            $.ajax({
                url: '{{ route('expedisi-surjal.show') }}', // Ganti dengan endpoint yang sesuai
                type: 'GET',
                data: {
                    id: id,
                    nomuat: nomuat
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        setButtonToUpdateMode();
                         // DATA DOKUMEN
                        $('#wilayah_expedisi').val(data.wilayah || 'denpasar');

                        // Customer
                        $('#customer_expedisi_id').val(data.customer_id || '');
                        $('#customer_expedisi').val(data.customer_name || '');
                        $('#customer_kode_expedisi').val(data.customer || '');

                        // Item
                        $('#item_expedisi_tipe').val(data.jenis_item || '');
                        $('#item_expedisi').val(data.pesanan || '');

                        // KENDARAAN & DRIVER
                        $('#kendaraan_expedisi_id').val(data.kendaraan_id || '');
                        $('#kendaraan_expedisi').val(data.kendaraan_nama || '');
                        $('#tgl_sj_expedisi').val(data.tgl_sj || '');
                        $('#no_sj_expedisi').val(data.no_sj || '');

                        // Driver 1
                        $('#driver_1_expedisi_id').val(data.driver_1_id || '');
                        $('#driver_1_expedisi').val(data.driver_1_nama || '');

                        // Driver 2
                        $('#driver_2_expedisi_id').val(data.driver_2_id || '');
                        $('#driver_2_expedisi').val(data.driver_2_nama || '');

                        // DATA PENERIMA
                        $('#penerima_expedisi').val(data.penerima || '');
                        $('#nama_penerima_expedisi').val(data.nama_penerima || '');
                        $('#phone_penerima_expedisi').val(data.phone_penerima || '');
                        $('#alamat_penerima_expedisi').val(data.alamat_penerima || '');

                        // DETAIL & PERHITUNGAN
                        $('#rute_expedisi').val(data.rute || '');
                        $('#jumlah_expedisi').val(parseFloat(data.jumlah) || 0);
                        $('#harga_expedisi').val(data.harga || 0);
                        $('#disc_expedisi').val(parseFloat(data.disc_percent) || 0);
                        $('#del_charge_expedisi').val(data.del_charge || 0);

                        // Button Print PDF
                        $('#divPrintSuratJalan').removeClass('d-none');
                        $('#btnPrintSuratJalan').attr('data-id',id);

                        // Hitung total otomatis
                        calculateTotal();

                        // // Tampilkan pesan sukses
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Data Dimuat',
                        //     text: 'Data muatan berhasil dimuat ke form',
                        //     timer: 2000,
                        //     showConfirmButton: false
                        // });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal memuat data'
                        });
                    }
                },
                error: function(xhr) {
                    $('#loading_modal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data'
                    });
                },
                complete: function() {
                    $('#loading_modal').modal('hide');
                }
            });
        });
    });
    // ======================== End Of Show Detail Surjal =============================
    // ================================= Pilih Customer =====================================
    $('#customer_expedisi_btn').click(function(e) {
        e.preventDefault();
        $('#customerModalExp').modal('show');
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalCusExpTable')) {
            $('#modalCusExpTable').DataTable().destroy();
        }
        var table = $('#modalCusExpTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("expedisi-cus.data") }}',
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
    $(document).on('click', '.view-btn-customer-expedisi', function(e) {
        e.preventDefault();
        var kodeCus = $(this).data('id');
        var namaCus = $(this).data('name');
        var CUSTOMER = $(this).data('customer');
        // Mengisi nilai ke elemen yang dituju
        $('#customer_expedisi_id').val(kodeCus);
        $('#customer_expedisi').val(namaCus);
        $('#customer_kode_expedisi').val(CUSTOMER);
        // Kosongkan dulu item
        $('#item_expedisi_id').val('');
        $('#item_expedisi_item').val('');
        $('#item_expedisi').val('');

        // Tutup modal
        $('#customerModalExp').modal('hide');
    });
    // ============================== End Of Pilih Customer ==================================
    // =================================== Pilih Item =====================================
    $(document).on('click', '#item_expedisi_btn', function(e) {
        var expedisiId = $('#customer_expedisi_id').val();

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

        $('#itemModalExp').modal('show');

        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalItemExpTable')) {
            $('#modalItemExpTable').DataTable().destroy();
        }

        // rebuild datatable
        $('#modalItemExpTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('price-customer-modal.price', ':kode') }}".replace(':kode', expedisiId),
                dataSrc: function (json) {
                    // SET INFO CUSTOMER DI ATAS TABEL
                    $("#custNameExp").text(json.customer_nama);
                    $("#custKodeExp").text(json.customer_kode);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'KETERANGAN' },
                { data: 'DARI' },
                { data: 'SAMPAI' },
                { data: 'nama_rute' },
                { data: 'harga_html', orderable: false, searchable: false },
                { data: 'jenis_text' },
                { data: 'aksi', orderable: false, searchable: false }
            ]
        });
    });
    // ### Select Button
    $(document).on('click', '.pick-price-exp', function(e) {
        e.preventDefault();
        var kodeCus = $(this).data('id');
        var kodeJenis = $(this).data('jenis');
        // Ambil KETERANGAN dari kolom di baris yang sama
        var row = $(this).closest('tr');
        var keterangan = row.find('td:eq(1)').text();
        var rute = row.find('td:eq(4)').text();
        var harga = row.find('td:eq(5)').text().trim();

        // Mengisi nilai ke elemen yang dituju
        $('#jumlah_expedisi').val(1);
        $('#item_expedisi_id').val(kodeCus);
        $('#item_expedisi_tipe').val(kodeJenis);
        $('#item_expedisi').val(keterangan);
        $('#rute_expedisi').val(rute);
        $('#harga_expedisi').val(harga);
        calculateTotal();
        // Tutup modal
        $('#itemModalExp').modal('hide');
    });
    // =============================== End Of Pilih Item ==================================
    // =================================== Pilih Kendaraan =====================================
    $(document).on('click', '#kendaraan_expedisi_btn', function(e) {

        $('#kendaraanModalExp').modal('show');

        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalKendaraanExpTable')) {
            $('#modalKendaraanExpTable').DataTable().destroy();
        }

        // rebuild datatable
        $('#modalKendaraanExpTable').DataTable({
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
        $('#kendaraan_expedisi_id').val(keterangan);
        $('#kendaraan_expedisi').val(nama);

        // Tutup modal
        $('#kendaraanModalExp').modal('hide');
    });
    // =============================== End Of Pilih Kendaraan ==================================
    // =================================== Pilih Driver =====================================
    $(document).on('click', '#driver_1_expedisi_btn, #driver_2_expedisi_btn', function(e) {
        e.preventDefault();
        var kodeKen = $(this).data('id');
        // Simpan kodeKen ke modal sebagai data atribut
        $('#driverModalExp').data('kode-ken', kodeKen);
        $('#driverModalExp').modal('show');

        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalDriverExpTable')) {
            $('#modalDriverExpTable').DataTable().destroy();
        }

        // rebuild datatable
        $('#modalDriverExpTable').DataTable({
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
        // Simpan kodeKen ke modal sebagai data atribut
        var kodeKen = $('#driverModalExp').data('kode-ken');
        var kodeDriver = $(this).data('id');
        // Ambil KETERANGAN dari kolom di baris yang sama
        var row = $(this).closest('tr');
        var nama = row.find('td:eq(2)').text();

        // Cek kodeKen untuk menentukan field mana yang diisi
        if (kodeKen == 1) {
            $('#driver_1_expedisi_id').val(kodeDriver);
            $('#driver_1_expedisi').val(nama);
        } else if (kodeKen == 2) {
            $('#driver_2_expedisi_id').val(kodeDriver);
            $('#driver_2_expedisi').val(nama);
        }

        // Tutup modal
        $('#driverModalExp').modal('hide');
    });
    // =============================== End Of Pilih Driver ==================================
    // ============================ Submit Data Form =================================
    // Hitung otomatis
    $('#jumlah_expedisi, #harga_expedisi, #disc_expedisi, #del_charge_expedisi').on('input', function() {
        calculateTotal();
    });

    // Auto Del Charge
    $('#auto_dc_expedisi').click(function(e) {
        e.preventDefault();
        const jumlah = parseNumber($('#jumlah_expedisi').val()) || 0;
        const harga = parseNumber($('#harga_expedisi').val()) || 0;
        const autoDC = (jumlah * harga) * 0.05; // 5% dari total
        $('#del_charge_expedisi').val(Math.round(autoDC));
        calculateTotal();
    });

    // Tombol Simpan
    $('#buttonSimpanExp').click(function(e) {
        e.preventDefault();

        // Validasi form
        if (!validateForm()) {
            return;
        }
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            // Jalankan setelah modal benar2 muncul
            submitForm();
        });
    });

    function validateForm() {
        let isValid = true;
        const errors = [];

        // Validasi required fields
        if (!$('#tgl_sj_expedisi').val()) {
            errors.push('Tgl Muat harus diisi');
            isValid = false;
        }
        if (!$('#customer_expedisi').val()) {
            errors.push('Customer harus dipilih');
            isValid = false;
        }
        if (!$('#rute_expedisi').val()) {
            errors.push('Rute harus diisi');
            isValid = false;
        }
        if (!$('#jumlah_expedisi').val() || parseNumber($('#jumlah_expedisi').val()) <= 0) {
            errors.push('Jumlah harus diisi dan lebih dari 0');
            isValid = false;
        }

        // ===============================
        // VALIDASI TABEL EXPEDISI
        // ===============================
        let tbody = $('#tableProsesExpedisi tbody');
        let rowCount = tbody.find('tr').length;

        if (rowCount > 0) {
            // Ambil jenis dari row pertama
            let jenisTabel = tbody
                .find('tr:first td[data-jenis]')
                .data('jenis');

            let jenisInput = $('#item_expedisi_tipe').val();

            if (jenisInput && jenisTabel != jenisInput) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tipe Item Berbeda',
                    html: `
                        Tipe item yang dipilih <b>tidak sama</b> dengan data di tabel.<br><br>
                        <b>Solusi:</b><br>
                        • Hapus data di tabel terlebih dahulu<br>
                        • Atau submit data yang sudah ada
                    `,
                    confirmButtonText: 'OK'
                });
                return false;
            }
        }

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errors.join('<br>'),
                confirmButtonText: 'OK'
            });
        }

        return isValid;
    }

    function submitForm() {
        // Prepare data
        const formData = {
            // DOKUMEN
            NOMUAT: $('#no_muat_expedisi').val(),
            TGLMUAT: $('#tgl_muat_expedisi').val(),
            NOJALAN: $('#nomor_perjalanan_expedisi').val(),
            WILAYAH: $('#wilayah_expedisi').val(),
            CUSTOMER: $('#customer_kode_expedisi').val(),
            customer_expedisi_id: $('#customer_expedisi_id').val(),
            item_expedisi_id: $('#item_expedisi_id').val(),
            item_expedisi: $('#item_expedisi').val(),

            // KENDARAAN & DRIVER
            KENDARAAN: $('#kendaraan_expedisi').val(),
            kendaraan_expedisi_id: $('#kendaraan_expedisi_id').val(),
            NAMA_KENDARAAN: $('#kendaraan_expedisi').val(),
            tglsj: $('#tgl_sj_expedisi').val(),
            NOSJ: $('#no_sj_expedisi').val(),
            DRIVER: $('#driver_1_expedisi').val(),
            driver_1_expedisi_id: $('#driver_1_expedisi_id').val(),
            NAMA_DRIVER: $('#driver_1_expedisi').val(),
            DRIVER2: $('#driver_2_expedisi').val(),
            driver_2_expedisi_id: $('#driver_2_expedisi_id').val(),
            NAMA_DRIVER2: $('#driver_2_expedisi').val(),

            // PENERIMA
            P_PENERIMA: $('#penerima_expedisi').val(),
            P_NAMA: $('#nama_penerima_expedisi').val(),
            P_PHONE: $('#phone_penerima_expedisi').val(),
            P_ALAMAT: $('#alamat_penerima_expedisi').val(),

            // DETAIL & PERHITUNGAN
            rute: $('#rute_expedisi').val(),
            JUMLAH: parseNumber($('#jumlah_expedisi').val()) || 0,
            UNIT: 'KG', // Default KG, bisa diganti sesuai kebutuhan
            HARGA: parseNumber($('#harga_expedisi').val()) || 0,
            hargaaw: parseNumber($('#harga_expedisi').val()) || 0,
            DISC: parseFloat($('#disc_expedisi').val()) || 0,
            DC: parseNumber($('#del_charge_expedisi').val()) || 0,
            DCAW: parseNumber($('#del_charge_expedisi').val()) || 0,

            // Hitung otomatis
            NDISC: parseNumber($('#sub_total_expedisi').val()) * (parseFloat($('#disc_expedisi').val()) / 100) || 0,
            NDISCAW: parseNumber($('#sub_total_expedisi').val()) * (parseFloat($('#disc_expedisi').val()) / 100) || 0,
            TOTAL: parseNumber($('#dpp_expedisi').val()) || 0,
            PPN: parseFloat($('#ppn_expedisi').val()), // Default 11%
            GRAND: parseNumber($('#grand_total_expedisi').val()) || 0,

            // STATUS & DEFAULT VALUES
            JENISHRG: $('#item_expedisi_tipe').val(), // Default jenis harga
            JENIS: 'EKS', // Ekspedisi
            STS: 'INVOICE',
            SIMPAN: 'N',
            READY: 'Y',
            CLOSSING: 'N',
            KETERANGAN: 'EXP ' + ($('#item_expedisi').val() || '') + ' ' + ($('#customer_expedisi').val() || '') + ' KE ' + ($('#penerima_expedisi').val() || ''),

            // User info (bisa dari session)
            user: 'USER' + new Date().toLocaleDateString('id-ID'),
            // _token: $('meta[name="csrf-token"]').attr('content') // CSRF token untuk Laravel
        };

        // console.log('Data yang dikirim:', formData); // Untuk debugging
        var url = $('#no_sj_expedisi').val() ? "{{ route('expedisi.update', ':nosj') }}".replace(':nosj', $('#no_sj_expedisi').val()): "{{ route('expedisi.store') }}";
        // AJAX Request
        $.ajax({
            url: url, // Ganti dengan route Laravel Anda
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                $('#loading_modal').modal('hide');
                if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan',
                    html: `
                        <div class="text-start">
                            <p><strong>NO MUAT:</strong> ${response.data.NOMUAT}</p>
                            <p><strong>NO SJ:</strong> ${response.data.NOSJ || '-'}</p>
                            <p><strong>TOTAL:</strong> Rp ${response.data.GRAND}</p>
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6',
                    showCancelButton: false,
                    focusConfirm: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (userRole === 'admin') {
                            addRowExpedisi({
                                NOSJ: response.data.NOSJ,
                                tglsj: $('#tgl_sj_expedisi').val(),
                                JUMLAH: formData.JUMLAH,
                                HARGA: parseNumber(formData.HARGA),
                                DC: parseNumber(formData.DC),
                                NDISC: formData.DISC,
                                PPN: formData.PPN,
                                JENISHRG: formData.JENISHRG,
                                GRAND: parseNumber(formData.GRAND),
                            });
                        }
                        resetFormExpedisi();
                    }
                });
            } else {
                    $('#loading_modal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $('#loading_modal').modal('hide');

                let errorMessage = 'Terjadi kesalahan pada server';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    }
    // ======================== End Of Submit Data Form =============================
    // ============================= Show Detail No Muat ================================
    // Event listener untuk tombol pickMuat
    $(document).on('click', '.pickMuat', function() {
        const id = $(this).data('id');
        const nomuat = $(this).data('nomuat');

        $('#muatModalExp').modal('hide');
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            // Ambil data dari server berdasarkan id/nomuat
            $.ajax({
                url: '{{ route('expedisi.show') }}', // Ganti dengan endpoint yang sesuai
                type: 'GET',
                data: {
                    id: id,
                    nomuat: nomuat
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        setButtonToUpdateMode();
                         // DATA DOKUMEN
                        $('#tgl_muat_expedisi').val(data.tgl_muat || '');
                        $('#no_muat_expedisi').val(data.no_muat || '');
                        $('#wilayah_expedisi').val(data.wilayah || 'denpasar');
                        $('#nomor_perjalanan_expedisi').val(data.no_jalan || '');

                        // Customer
                        $('#customer_expedisi_id').val(data.customer_id || '');
                        $('#customer_expedisi').val(data.customer_name || '');
                        $('#customer_kode_expedisi').val(data.customer || '');

                        // Item
                        $('#item_expedisi').val(data.pesanan || '');

                        // KENDARAAN & DRIVER
                        $('#kendaraan_expedisi_id').val(data.kendaraan_id || '');
                        $('#kendaraan_expedisi').val(data.kendaraan_nama || '');
                        $('#tgl_sj_expedisi').val(data.tgl_sj || '');
                        $('#no_sj_expedisi').val(data.no_sj || '');

                        // Driver 1
                        $('#driver_1_expedisi_id').val(data.driver_1_id || '');
                        $('#driver_1_expedisi').val(data.driver_1_nama || '');

                        // Driver 2
                        $('#driver_2_expedisi_id').val(data.driver_2_id || '');
                        $('#driver_2_expedisi').val(data.driver_2_nama || '');

                        // DATA PENERIMA
                        $('#penerima_expedisi').val(data.penerima || '');
                        $('#nama_penerima_expedisi').val(data.nama_penerima || '');
                        $('#phone_penerima_expedisi').val(data.phone_penerima || '');
                        $('#alamat_penerima_expedisi').val(data.alamat_penerima || '');

                        // DETAIL & PERHITUNGAN
                        $('#rute_expedisi').val(data.rute || '');
                        $('#jumlah_expedisi').val(parseFloat(data.jumlah) || 0);
                        $('#harga_expedisi').val(data.harga || 0);
                        $('#disc_expedisi').val(parseFloat(data.disc_percent) || 0);
                        $('#del_charge_expedisi').val(data.del_charge || 0);

                        // Button Print PDF
                        $('#divPrintSuratJalan').removeClass('d-none');
                        $('#btnPrintSuratJalan').attr('data-id',id);

                        // Hitung total otomatis
                        calculateTotal();

                        // // Tampilkan pesan sukses
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Data Dimuat',
                        //     text: 'Data muatan berhasil dimuat ke form',
                        //     timer: 2000,
                        //     showConfirmButton: false
                        // });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message || 'Gagal memuat data'
                        });
                    }
                },
                error: function(xhr) {
                    $('#loading_modal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengambil data'
                    });
                },
                complete: function() {
                    $('#loading_modal').modal('hide');
                }
            });
        });
    });
    // ======================== End Of Show Detail No Muat =============================
    // =========================== Print PDF ================================
    $('#btnPrintSuratJalan').on('click', function () {
        let id = $(this).data('id');
        let url = "{{ route('expedisi.printSuratJalan', ':id') }}";
        url = url.replace(':id', id);
        window.open(url, '_blank');
    });
    // ======================== End Of Print PDF =============================
    // ============================ Clear Form =============================
    $('#buttonClearExp').click(function() {
        resetFormExpedisi();
    });
    // ======================== End Of Clear Form =============================
    // ================================= Pilih No Muat =====================================
    $('#muat_expedisi_btn').click(function(e) {
        e.preventDefault();
        $('#muatModalExp').modal('show');
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#modalMuatExpTable')) {
            $('#modalMuatExpTable').DataTable().destroy();
        }
        var tableMuat = $('#modalMuatExpTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url: "{{ route('expedisi.data') }}",
                data: function(d) {
                    d.tgl_mulai = $('#filter_tgl_mulai').val();
                    d.tgl_akhir = $('#filter_tgl_akhir').val();
                    d.search_muat = $('#filter_muat').val();
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

        $('#btn_filter_muat').click(function () {
            tableMuat.ajax.reload();
        });
    });
    // ============================= End Of Pilih No Muat =====================================
    // ================================ Delete No Muat ======================================
    $(document).on('click', '.deleteMuat', function () {
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
                let url = "{{ route('expedisi.destroy', ':id') }}";
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
                        $('#modalMuatExpTable').DataTable().ajax.reload(null, false);
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
    // ============================ End Of Delete No Muat ====================================
    // ============================ Simpan Submit No Muat =================================
    $('#simpanMuatExpBtn').on('click', function () {
        // if (!validateForm()) return;

        let nosjList = [];

        $('#tableProsesExpedisi tbody tr').each(function () {
            let nosj = $(this).find('td:eq(1)').text().trim();
            if (nosj && nosj !== '-') {
                nosjList.push(nosj);
            }
        });

        if (nosjList.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kosong',
                text: 'Tidak ada NO SJ untuk disimpan'
            });
            return;
        }

        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            // Jalankan setelah modal benar2 muncul
            submitFormMuat();
        });
        function submitFormMuat() {
            $.ajax({
                url: '{{ route('expedisi-muat.store') }}',
                method: 'POST',
                data: {
                    nosj: nosjList
                },
                success: function (res) {
                    $('#loading_modal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    });
                    deleteRowTabelMuat();
                },
                error: function () {
                    $('#loading_modal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        }
    });
    // ======================== End of Simpan Submit No Muat =============================
    // =========================== Hapus row tabel generate no muat  ===========================
    $(document).on('click', '.btn-hapus-row', function () {
        let row = $(this).closest('tr');
        row.remove();
        $('#tableProsesExpedisi tbody tr').each(function (index) {
            $(this).find('td:eq(0)').text(index + 1);
        });
        hitungGrandTotal();
        // 🔽 CEK JIKA TABEL KOSONG
        let totalRow = $('#tableProsesExpedisi tbody tr').length;

        if (totalRow === 0) {
            $('#simpanMuatExpBtn').hide();
            $('#clearMuatExpBtn').hide();
        }
    });
    // ======================= End Of Hapus row tabel generate no muat  ==========================
    // =============================== Clear Table No Muat ===================================
    $('#clearMuatExpBtn').on('click', function () {
        // Hapus semua row tabel
        deleteRowTabelMuat();
    });
    // ========================== End Of Clear Table No Muat =================================
    // ########################################################################
    // FUNCTION HELPER:
    // ########################################################################
    // Fungsi untuk format number
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseNumber(str) {
        if (!str) return 0;
        return parseFloat(str.toString().replace(/\./g, '').replace(',', '.'));
    }

    // Menghitung nilai total di form expedisi
    function calculateTotal() {
        // Ambil nilai dari form
        const jumlah = parseNumber($('#jumlah_expedisi').val()) || 0;
        const harga = parseNumber($('#harga_expedisi').val()) || 0;
        const discPercent = parseFloat($('#disc_expedisi').val()) || 0;
        const delCharge = parseNumber($('#del_charge_expedisi').val()) || 0;
        const ppnPercent = parseNumber($('#ppn_expedisi').val()) || 0;

        // ===========================================
        // PERHITUNGAN YANG BENAR:
        // ===========================================

        // 1. Sub total (sebelum diskon)
        const subTotal = jumlah * harga;

        // 2. Diskon dalam rupiah
        const discAmount = subTotal * (discPercent / 100);

        // 3. Total setelah diskon (DPP)
        const dpp = subTotal - discAmount;

        // 4. PPN langsung masuk ke grand total (tanpa tampilkan nominal)
        const ppnAmount = dpp * (ppnPercent / 100);

        // 5. Grand total (DPP + PPN + Del Charge)
        const grandTotal = dpp + ppnAmount + delCharge;

        // ===========================================
        // TAMPILKAN HASIL:
        // ===========================================
        // Hanya tampilkan yang perlu
        $('#sub_total_expedisi').val(formatNumber(Math.round(subTotal)));
        $('#dpp_expedisi').val(formatNumber(Math.round(dpp)));
        $('#grand_total_expedisi').val(formatNumber(Math.round(grandTotal)));
    }

    // =================== Pajak PPN ==========================
    function loadInputPajak(){
        $.ajax({
            url: '{{ route('get_pajak') }}',
            type: 'GET',
            success: function(response) {
                let nilai_ppn = response.data.ppn;
                $('#ppn_expedisi').val(nilai_ppn);
            },
            error: function() {
                $('#ppn_expedisi').val('Error Loading');
            }
        });
    }

    function setButtonToUpdateMode() {
        $('#buttonSimpanExp')
            .removeClass('btn-info')
            .addClass('btn-success')
            .html('<i class="bx bx-edit me-1"></i>UPDATE [F3]')
            .attr('title', 'Update data ekspedisi')
            .data('mode', 'update')

        // Tambahkan badge info
        $('#buttonSimpanExp').append('<span class="badge bg-light text-dark ms-2">EDIT MODE</span>');
    }

    // Fungsi untuk mengubah tombol ke mode CREATE/SIMPAN
    function setButtonToSaveMode() {
        $('#buttonSimpanExp')
            .removeClass('btn-success')
            .addClass('btn-info')
            .html('<i class="bx bx-save me-1"></i>SIMPAN [F3]')
            .attr('title', 'Simpan data ekspedisi baru')
            .data('mode', 'save')
            .data('id', '')
            .find('.badge').remove(); // Hapus badge jika ada
            $('#divPrintSuratJalan').addClass('d-none');
    }

    // Fungsi Untuk Me Reset Form Expedisi
    function resetFormExpedisi() {
        // Reset semua input text, number, textarea
        $('input[type="text"], input[type="number"], textarea').val('');
        // Reset select
        $('select').prop('selectedIndex', 0);
        // Field khusus
        $('#sub_total_expedisi, #dpp_expedisi, #ppn_expedisi, #grand_total_expedisi').val('');
        $('#wilayah_expedisi').val('denpasar');
        // Set default tanggal muat
        $('#tgl_sj_expedisi').val(new Date().toISOString().split('T')[0]);
        setButtonToSaveMode();
        loadInputPajak();
    }

    // fungsi merubah tabel menjadi
    function nvl(val) {
        return val ? val : 0;
    }

    // MenambahBaris Tabel No Muat
    function addRowExpedisi(data) {
        let tbody = $('#tableProsesExpedisi tbody');
        let nosjBaru = (data.NOSJ || '').toString().trim();

        // Cek duplikat NOSJ
        let sudahAda = false;
        tbody.find('tr').each(function () {
            let nosjTabel = $(this).find('td:eq(1)').text().trim(); // kolom NOSJ
            if (nosjTabel === nosjBaru && nosjBaru !== '') {
                sudahAda = true;
                return false; // break loop
            }
        });

        if (sudahAda) {
            Swal.fire({
                icon: 'warning',
                title: 'Duplikat NO SJ',
                text: `NO SJ ${nosjBaru} sudah ada di tabel`,
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        let no = tbody.find('tr').length + 1;

        let row = `
            <tr>
                <td class="text-center">${no}</td>
                <td>${nosjBaru || '-'}</td>
                <td>${data.tglsj || '-'}</td>
                <td class="text-end">${nvl(data.DC)}</td>
                <td class="text-end">${nvl(data.JUMLAH)}</td>
                <td class="text-center">KG</td>
                <td data-jenis="${data.JENISHRG}">
                    ${getJenisHargaLabel(data.JENISHRG)}
                </td>
                <td class="text-end">${nvl(data.HARGA)}</td>
                <td class="text-end">${nvl(data.NDISC)}</td>
                <td class="text-end">${nvl(data.PPN)}</td>
                <td class="text-end">${formatNumber(nvl(data.GRAND))}</td>

                <td class="text-center">
                    <button class="btn btn-danger btn-sm btn-hapus-row">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        // untuk convert janis harga
        function getJenisHargaLabel(val) {
            if (val == 1) return 'Eceran';
            if (val == 2) return 'Booking';
            return '-';
        }

        tbody.append(row);
        hitungGrandTotal();
        $('#simpanMuatExpBtn').show();
        $('#clearMuatExpBtn').show();
    }

    // Menghitung Lumalah Tabel No Muat
    function hitungGrandTotal() {
        let total = 0;

        $('#tableProsesExpedisi tbody tr').each(function () {
            let nilai = $(this).find('td:eq(10)').text();
            total += parseNumber(nilai) || 0;
        });

        $('#grandTotal').text(total.toLocaleString('id-ID'));
    }

    // Hapus semua row tabel no muat
    function deleteRowTabelMuat() {
        $('#tableProsesExpedisi tbody').empty();
        hitungGrandTotal();
        $('#simpanMuatExpBtn').hide();
        $('#clearMuatExpBtn').hide();
        $('#no_muat_expedisi').val('');
        $('#tgl_muat_expedisi').val('');
    }

    // function hitungExpedisi() {
    //     let jumlah = parseFloat($('#jumlah_expedisi').val()) || 0;
    //     let harga  = parseFloat($('#harga_expedisi').val()) || 0;
    //     let disc   = parseFloat($('#disc_expedisi').val()) || 0;
    //     let ppnPersen = parseFloat($('#ppn_expedisi').val()) || 0; // 11 (tetap)

    //     // SUB TOTAL
    //     let subTotal = jumlah * harga;

    //     // DPP (setelah diskon)
    //     let nilaiDisc = subTotal * (disc / 100);
    //     let dpp = subTotal - nilaiDisc;

    //     // PPN (dipakai INTERNAL, tidak ditampilkan)
    //     let ppn = dpp * (ppnPersen / 100);

    //     // GRAND TOTAL
    //     let grandTotal = dpp + ppn;

    //     // TAMPILKAN (HANYA YANG PERLU)
    //     $('#sub_total_expedisi').val(subTotal.toLocaleString('id-ID'));
    //     $('#dpp_expedisi').val(dpp.toLocaleString('id-ID'));
    //     $('#grand_total_expedisi').val(grandTotal.toLocaleString('id-ID'));
    // }
    // // Trigger otomatis
    // $('#jumlah_expedisi, #harga_expedisi, #disc_expedisi').on('keyup change', hitungExpedisi);
});
</script>

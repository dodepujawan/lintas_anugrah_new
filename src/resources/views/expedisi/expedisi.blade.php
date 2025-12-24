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
            <div class="col-md-3">
                <label class="form-label">TGL MUAT</label>
                <input type="date" class="form-control form-control-sm" id="tgl_muat_expedisi" name="tgl_muat_expedisi">
            </div>
            <div class="col-md-3">
                <label class="form-label">NO MUAT</label>
                <input type="text" class="form-control form-control-sm" id="no_muat_expedisi" name="no_muat_expedisi" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">WILAYAH</label>
                <select class="form-select form-select-sm" id="wilayah_expedisi" name="wilayah_expedisi">
                    <option value="denpasar">DENPASAR</option>
                    <option value="gianyar">GIANYAR</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">NOMOR PERJALANAN</label>
                <input type="text" class="form-control form-control-sm" id="nomor_perjalanan_expedisi" name="nomor_perjalanan_expedisi">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mt-2">
                <label class="form-label">CUSTOMER</label>
                <div class="input-group input-group-sm">
                    <input type="hidden" name="customer_expedisi_id" id="customer_expedisi_id">
                    <input type="text" class="form-control" id="customer_expedisi" name="customer_expedisi">
                    <button class="btn btn-outline-secondary" id="customer_expedisi_btn"><i class="bx bx-search"></i></button>
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <label class="form-label">ITEM</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="item_expedisi_id" id="item_expedisi_id">
                    <input type="text" class="form-control" id="item_expedisi" name="item_expedisi">
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
            <div class="col-md-6">
                <label class="form-label">KENDARAAN</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="kendaraan_expedisi" name="kendaraan_expedisi">
                    <button class="btn btn-outline-secondary"><i class="bx bx-search"></i></button>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">TGL SJ</label>
                <input type="date" class="form-control form-control-sm" id="tgl_sj_expedisi" name="tgl_sj_expedisi">
            </div>
            <div class="col-md-3">
                <label class="form-label">NO SJ</label>
                <input type="text" class="form-control form-control-sm" id="no_sj_expedisi" name="no_sj_expedisi">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <label class="form-label">DRIVER I</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="driver_1_expedisi" name="driver_1_expedisi">
                    <button class="btn btn-outline-secondary"><i class="bx bx-search"></i></button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">DRIVER II</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="driver_2_expedisi" name="driver_2_expedisi">
                    <button class="btn btn-outline-secondary"><i class="bx bx-search"></i></button>
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
                <select class="form-select form-select-sm" id="rute_expedisi" name="rute_expedisi"></select>
            </div>
            <div class="col-md-2">
                <label class="form-label">JUMLAH</label>
                <input type="number" class="form-control form-control-sm" id="jumlah_expedisi" name="jumlah_expedisi">
            </div>
            <div class="col-md-2">
                <label class="form-label">HARGA @</label>
                <input type="number" class="form-control form-control-sm" id="harga_expedisi" name="harga_expedisi">
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

    {{-- BIAYA & BKK (FINANCE) --}}
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-wallet'></i> BIAYA & BKK</h5>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">NO BKK</label>
                <input type="text" class="form-control form-control-sm" id="no_bkk_expedisi" name="no_bkk_expedisi">
            </div>
            <div class="col-md-4">
                <label class="form-label">BIAYA</label>
                <input type="number" class="form-control form-control-sm" id="biaya_expedisi" name="biaya_expedisi">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary btn-sm me-2" id="input_biaya_expedisi" name="input_biaya_expedisi">INPUT BIAYA</button>
                <button class="btn btn-secondary btn-sm" id="refresh_bkk_expedisi" name="refresh_bkk_expedisi">REFRESH</button>
            </div>
        </div>
    </div>

    <!-- Deteksi ADADC Section -->
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-error-circle me-2'></i>DETEKSI ADADC</h5>
            <span class="status-deteksi">
                <i class='bx bx-circle me-1'></i>
                <span>0</span>
            </span>
        </div>
        <div class="alert alert-warning alert-sm mb-0">
            <small>
                <i class='bx bx-info-circle me-1'></i>
                Jika terdeteksi ada Delcharge Klik <strong>Auto DC</strong> untuk isi DC KET
            </small>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card-expedisi">
        <div class="row g-2">
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-info btn-action w-100" id="buttonSimpan">
                    <i class='bx bx-save me-1'></i>SIMPAN [F3]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-danger btn-action w-100" id="buttonClear">
                    <i class='bx bx-trash me-1'></i>Clear [F6]
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Data SJ -->
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-table me-2'></i>DATA SURAT JALAN</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead>
                    <tr>
                        <th width="30">NO</th>
                        <th width="120">NO.SJ</th>
                        <th width="100">TGL.SJ</th>
                        <th width="120">PESANAN AWAL</th>
                        <th width="50">GB</th>
                        <th width="100">DELCHARGE</th>
                        <th width="120">PESANAN GABUNG</th>
                        <th width="80">JUMLAH</th>
                        <th width="80">UNIT</th>
                        <th width="100">HARGA</th>
                        <th width="80">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>SJ001</td>
                        <td>22-12-2025</td>
                        <td>P001</td>
                        <td class="text-center">
                            <span class="badge bg-success">Y</span>
                        </td>
                        <td class="text-end">50,000</td>
                        <td>PG001</td>
                        <td class="text-end">10</td>
                        <td class="text-center">PCS</td>
                        <td class="text-end">100,000</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-action">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>SJ002</td>
                        <td>22-12-2025</td>
                        <td>P002</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">N</span>
                        </td>
                        <td class="text-end">25,000</td>
                        <td>PG002</td>
                        <td class="text-end">5</td>
                        <td class="text-center">BOX</td>
                        <td class="text-end">250,000</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-action">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-action">
                                <i class='bx bx-trash'></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card-expedisi">
        <div class="row g-2">
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-primary btn-action w-100">
                    <i class='bx bx-file me-1'></i>NEW [F1]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-success btn-action w-100">
                    <i class='bx bx-plus-circle me-1'></i>TAMBAH [F2]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-info btn-action w-100">
                    <i class='bx bx-save me-1'></i>SIMPAN [F3]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-warning btn-action w-100">
                    <i class='bx bx-edit me-1'></i>EDIT [F4]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-secondary btn-action w-100">
                    <i class='bx bx-x-circle me-1'></i>BATAL [F5]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-danger btn-action w-100">
                    <i class='bx bx-trash me-1'></i>HAPUS [F6]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-outline-primary btn-action w-100">
                    <i class='bx bx-arrow-back me-1'></i>RETUR SJ [F7]
                </button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-outline-danger btn-action w-100">
                    <i class='bx bx-log-out me-1'></i>KELUAR [F12]
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Customer --}}
<div class="modal fade" id="customerModalExp" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="modalCusExpTable">
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
                    <tbody></tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // ================================= Pilih Customer =====================================
    $('#customer_expedisi_btn').click(function() {
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
    })

    // ### Select Button
    $(document).on('click', '.view-btn-customer-expedisi', function() {
        var kodeCus = $(this).data('id');
        var namaCus = $(this).data('name');

        // Mengisi nilai ke elemen yang dituju
        $('#customer_expedisi_id').val(kodeCus);
        $('#customer_expedisi').val(namaCus); // Kosongkan dulu

        // Tutup modal
        $('#customerModalExp').modal('hide');
    });
    // ============================== End Of Pilih Customer ==================================
});
</script>

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
</style>
<div class="container-fluid mt-3 mb-5">
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
                    <div class="col-md-8">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label">NO.MUAT</label>
                                <input type="text" id="no_muat_rent_dingin" name="no_muat_rent_dingin"
                                        class="form-control form-control-sm" value="MU20260000023" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">WILAYAH NOSJ</label>
                                <select id="wilayah_nosj_rent_dingin" name="wilayah_nosj_rent_dingin"
                                        class="form-select form-select-sm">
                                    <option selected>DENPASAR</option>
                                    <option>JAKARTA</option>
                                    <option>SURABAYA</option>
                                    <option>BANDUNG</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-sm btn-outline-primary btn-action mt-4">CARI</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">TANGGAL</label>
                                <input type="date" id="tanggal_rent_dingin" name="tanggal_rent_dingin"
                                        class="form-control form-control-sm" value="2026-01-06">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">TANGGAL INVOICE</label>
                                <input type="date" id="tanggal_inv_rent_dingin" name="tanggal_inv_rent_dingin"
                                        class="form-control form-control-sm" value="2026-01-06">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Customer -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="driver-section">
                            <div class="driver-header">DATA CUSTOMER</div>
                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label">NAMA</label>
                                    <input type="text" id="customer_rent_dingin" name="customer_rent_dingin"
                                            class="form-control form-control-sm" placeholder="Masukkan nama customer">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TELPON</label>
                                    <input type="text" id="telpon_rent_dingin" name="telpon_rent_dingin"
                                            class="form-control form-control-sm" placeholder="Masukkan nomor telepon">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label class="form-label">ALAMAT</label>
                                    <textarea id="alamat_rent_dingin" name="alamat_rent_dingin"
                                                class="form-control form-control-sm" rows="2" placeholder="Masukkan alamat customer"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Kendaraan & Driver -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="driver-section">
                            <div class="driver-header">DATA KENDARAAN</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">KENDARAAN</label>
                                    <select id="kendaraan_rent_dingin" name="kendaraan_rent_dingin"
                                            class="form-select form-select-sm">
                                        <option selected>Pilih kendaraan</option>
                                        <option>Truck Box Pendingin 10 Ton</option>
                                        <option>Minibus Pendingin 2 Ton</option>
                                        <option>Mobil Pendingin 5 Ton</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">JML.HARI</label>
                                    <input type="number" id="jml_hari_rent_dingin" name="jml_hari_rent_dingin"
                                            class="form-control form-control-sm" placeholder="0" min="1" value="1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">HARGA</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="harga_rent_dingin" name="harga_rent_dingin"
                                                class="form-control form-control-sm" placeholder="0" value="750000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="driver-section">
                            <div class="driver-header">DATA DRIVER</div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">DRIVER</label>
                                    <select id="driver_rent_dingin" name="driver_rent_dingin"
                                            class="form-select form-select-sm">
                                        <option selected>Pilih driver</option>
                                        <option>Budi Santoso</option>
                                        <option>Agus Wijaya</option>
                                        <option>Rudi Hartono</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label class="form-label">ITEM</label>
                                    <textarea id="item_rent_dingin" name="item_rent_dingin"
                                                class="form-control form-control-sm" rows="2" placeholder="Masukkan item yang akan diangkut"></textarea>
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
                                            class="form-control form-control-sm" value="750.000" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label class="form-label">DISCOUNT</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
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
                                            class="form-control form-control-sm" value="750.000" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-2">
                                <label class="form-label">PAJAK</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="pajak_rent_dingin" name="pajak_rent_dingin"
                                            class="form-control form-control-sm" value="75.000" readonly>
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
                                            class="form-control form-control-lg fw-bold" value="825.000" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Biaya -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="driver-section">
                            <div class="driver-header">BIAYA TAMBAHAN</div>

                            <!-- Input Biaya Baru -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label class="form-label">NO.BKK</label>
                                    <input type="text" id="no_bkk_rent_dingin" name="no_bkk_rent_dingin"
                                            class="form-control form-control-sm" placeholder="Masukkan nomor BKK">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">BIAYA</label>
                                    <input type="text" id="biaya_rent_dingin" name="biaya_rent_dingin"
                                            class="form-control form-control-sm" placeholder="Masukkan deskripsi biaya">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">INPUT BIAYA</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="input_biaya_rent_dingin" name="input_biaya_rent_dingin"
                                                class="form-control form-control-sm" placeholder="0">
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Aksi Biaya -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button class="btn btn-sm btn-outline-success btn-action">TAMBAH BIAYA</button>
                                    <button class="btn btn-sm btn-outline-warning btn-action">REFRESH BKK</button>
                                </div>
                            </div>

                            <!-- Tabel Daftar Biaya -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm table-custom">
                                    <thead>
                                        <tr>
                                            <th width="5%">NO</th>
                                            <th width="20%">NO.BKK</th>
                                            <th width="45%">BIAYA</th>
                                            <th width="20%">JUMLAH</th>
                                            <th width="10%">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td>BKK001</td>
                                            <td>Biaya Tol</td>
                                            <td class="text-end">Rp 150.000</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger btn-action">Hapus</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td>BKK002</td>
                                            <td>Biaya Parkir</td>
                                            <td class="text-end">Rp 75.000</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-danger btn-action">Hapus</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <div class="function-keys d-inline-block px-4 py-2 mb-3">
                            <span class="fw-bold">Function Keys:</span>
                            <span class="badge bg-secondary mx-1">F1 = NEW</span>
                            <span class="badge bg-secondary mx-1">F3 = SIMPAN</span>
                            <span class="badge bg-secondary mx-1">F4 = EDIT</span>
                            <span class="badge bg-secondary mx-1">F12 = KELUAR</span>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button class="btn btn-danger btn-action">
                                <i class="bi bi-box-arrow-left"></i> KELUAR [F12]
                            </button>
                            <button class="btn btn-success btn-action mx-2">
                                <i class="bi bi-plus-circle"></i> NEW [F1]
                            </button>
                            <button class="btn btn-primary btn-action">
                                <i class="bi bi-save"></i> SIMPAN [F3]
                            </button>
                            <button class="btn btn-warning btn-action mx-2">
                                <i class="bi bi-pencil"></i> EDIT [F4]
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Deteksi -->
            <div class="mt-3 text-end">
                <span class="status-deteksi">STATUS: DATA DITEMUKAN - NO. MUAT: MU20260000023</span>
            </div>
        </div>
    </div>
</div>

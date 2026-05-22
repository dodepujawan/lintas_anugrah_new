{{-- ============================= --}}
{{-- TABLE COOLROOM --}}
{{-- ============================= --}}
<div class="container mt-3" id="table_coolroom">
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">TRANSAKSI COOLROOM</h5>
            <button class="btn btn-primary btn-sm" id="btn_tambah_coolroom">
                <i class='bx bx-plus'></i> TRANSAKSI BARU
            </button>
        </div>
        <div class="table-responsive">
            <table id="CoolroomTable" class="table table-bordered table-striped w-100">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Grand</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
{{-- ============================= --}}
{{-- FORM COOLROOM --}}
{{-- ============================= --}}
<div class="container-fluid mt-2 d-none" id="form_coolroom">
    <div class="card shadow-sm border-0">
        {{-- HEADER --}}
        <div class="card-header py-2 d-flex justify-content-between align-items-center" style="background-color:#b7e1b0;">
            <button class="btn btn-sm btn-link text-decoration-none p-0" id="returnCoolroomBtn" style="color:#107af3;">
                <i class='bx bx-chevron-left'></i> Kembali
            </button>
            <h5 class="mb-0 fw-bold">FORM TRANSAKSI COOLROOM</h5>
            <div style="width:80px"></div>
        </div>

        <div class="card-body p-3">
            <input type="hidden" id="coolroom_id">
            {{-- TOP --}}
            <div class="row g-3">
                {{-- LEFT --}}
                <div class="col-lg-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body py-3">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="small fw-bold">CUSTOMER</label>
                                    <select id="customer_coolroom" class="form-select form-select-sm"></select>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">TANGGAL</label>
                                    <input type="date" id="tgl_coolroom" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">INVOICE</label>
                                    <input type="text" id="invoice_coolroom" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">JUMLAH KG</label>
                                    <input type="number" step="0.001" id="jumlah_coolroom" class="form-control form-control-sm text-end">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">HARGA @</label>
                                    <input type="text" id="harga_coolroom" class="form-control form-control-sm text-end">
                                </div>
                                <div class="col-12">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="boxing_coolroom">
                                        <label class="form-check-label small fw-bold" for="boxing_coolroom">
                                            Harga Boxing
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold">KETERANGAN</label>
                                    <textarea id="keterangan_coolroom" class="form-control form-control-sm" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="col-lg-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body py-3">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="small fw-bold">SUBTOTAL</label>
                                    <input type="text" id="subtotal_coolroom" class="form-control form-control-sm text-end" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">DISC %</label>
                                    <input type="number" id="disc_coolroom" class="form-control form-control-sm text-end" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">DISC Rp</label>
                                    <input type="text" id="ndisc_coolroom" class="form-control form-control-sm text-end" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold">DPP</label>
                                    <input type="text" id="dpp_coolroom" class="form-control form-control-sm text-end" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">PPN %</label>
                                    <input type="number" id="ppn_coolroom" class="form-control form-control-sm text-end" value="11">
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">PPN Rp</label>
                                    <input type="text" id="nppn_coolroom" class="form-control form-control-sm text-end" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="small fw-bold">GRAND TOTAL</label>
                                    <input type="text" id="grand_coolroom" class="form-control form-control-sm text-end fw-bold" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PAYMENT --}}
            <div class="card border-0 bg-secondary-subtle mt-3">
                <div class="card-body py-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-lg-3">
                            <label class="small fw-bold">BAYAR</label>
                            <input type="text" id="bayar_coolroom" class="form-control form-control-sm text-end">
                        </div>
                        <div class="col-lg-2">
                            <label class="small fw-bold">TOP</label>
                            <input type="number" id="top_coolroom" class="form-control form-control-sm">
                        </div>
                        <div class="col-lg-3">
                            <label class="small fw-bold">TGL JTP</label>
                            <input type="date" id="tgljt_coolroom" class="form-control form-control-sm">
                        </div>
                        <div class="col-lg-4">
                            <label class="small fw-bold">PIUTANG</label>
                            <input type="text" id="piutang_coolroom" class="form-control form-control-sm text-end fw-bold" readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-sm btn-primary" id="proses_coolroom">
                            SIMPAN
                        </button>
                        <button class="btn btn-sm btn-secondary" id="keluar_coolroom">
                            KELUAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

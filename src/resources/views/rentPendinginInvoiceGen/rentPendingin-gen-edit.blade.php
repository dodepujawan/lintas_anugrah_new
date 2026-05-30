<div class="container-fluid py-2">

    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm border-0 mb-2">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">INVOICE RENTAL</h4>
                <small class="text-muted">Data invoice rental mobil pendingin</small>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-dark btn-sm" id="btn_filter_invoice_ren">
                    <i class="fa fa-search"></i>
                    Reload
                </button>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- FILTER --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm border-0 mb-2">
        <div class="card-body py-2">
            <div class="row g-2">

                <div class="col-lg-2">
                    <label class="form-label mb-1">DARI</label>
                    <input type="date"
                        class="form-control form-control-sm"
                        id="tanggal_dari_invoice_ren">
                </div>

                <div class="col-lg-2">
                    <label class="form-label mb-1">SAMPAI</label>
                    <input type="date"
                        class="form-control form-control-sm"
                        id="tanggal_sampai_invoice_ren">
                </div>

                <div class="col-lg-3">
                    <label class="form-label mb-1">CARI</label>
                    <input type="text"
                        class="form-control form-control-sm"
                        id="search_invoice_ren"
                        placeholder="Invoice / Customer">
                </div>

            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- TABLE --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-2">

            <div class="table-responsive">

                <table class="table table-bordered table-hover table-sm align-middle w-100"
                    id="table_invoice_ren">

                    <thead class="table-dark">
                        <tr>
                            <th width="50">NO</th>
                            <th width="120">TGL</th>
                            <th width="170">INVOICE</th>
                            <th>CUSTOMER</th>
                            <th width="150" class="text-end">GRAND</th>
                            <th width="150" class="text-end">PIUTANG</th>
                            <th width="150" class="text-end">BAYAR</th>
                            <th width="100">AKSI</th>
                        </tr>
                    </thead>

                </table>

            </div>

        </div>
    </div>

</div>

{{-- ===================================================== --}}
{{-- MODAL EDIT --}}
{{-- ===================================================== --}}
<div class="modal fade"
    id="modal_invoice_edit_ren"
    tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-success text-white py-2">

                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        EDIT INVOICE RENTAL
                    </h5>
                </div>

                <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>

            </div>

            {{-- BODY --}}
            <div class="modal-body bg-light p-2">

                <div class="row g-2">

                    {{-- ================================================= --}}
                    {{-- KIRI --}}
                    {{-- ================================================= --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2">

                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">INVOICE</label>
                                    <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="invoice_invoice_edit_ren" readonly>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">CUSTOMER</label>
                                    <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="customer_invoice_edit_ren" readonly>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">KENDARAAN</label>
                                    <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="kendaraan_invoice_edit_ren" readonly>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">DRIVER</label>
                                    <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="driver_invoice_edit_ren" readonly>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">NO MUAT</label>
                                    <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="nomuat_invoice_edit_ren" readonly>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">TGL INV</label>
                                        <input type="date" class="form-control form-control-sm" id="tgl_invoice_invoice_edit_ren">
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">TGL JT</label>
                                        <input type="date" class="form-control form-control-sm" id="tgl_jt_invoice_edit_ren">
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">BAYAR</label>
                                        <input type="text" class="form-control form-control-sm text-end fw-bold bg-success-subtle" id="bayar_invoice_edit_ren">
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">PIUTANG</label>
                                        <input type="text" class="form-control form-control-sm text-end fw-bold bg-warning-subtle" id="piutang_invoice_edit_ren" readonly>
                                    </div>
                                </div>

                                <div class="d-flex align-items-end">
                                    <button class="btn btn-success btn-sm w-100" id="btn_simpan_invoice_edit_ren">
                                        <i class="bx bx-save"></i>
                                        SIMPAN
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ================================================= --}}
                    {{-- KANAN --}}
                    {{-- ================================================= --}}
                    <div class="col-lg-8">
                        <div class="row g-1">

                            <!-- RUTE -->
                            <div class="col-md-12">
                                <label class="fw-bold small mb-0 d-block">RUTE</label>
                                <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="rute_invoice_edit_ren" readonly>
                            </div>

                            <!-- JUMLAH -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">JUMLAH</label>
                                <input type="text" class="form-control form-control-sm text-end" id="jumlah_invoice_edit_ren">
                            </div>

                            <!-- HARGA -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">HARGA</label>
                                <input type="text" class="form-control form-control-sm text-end" id="harga_invoice_edit_ren">
                            </div>

                            <!-- SUBTOTAL -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">SUBTOTAL</label>
                                <input type="text" class="form-control form-control-sm text-end bg-secondary-subtle" id="subtotal_invoice_edit_ren" readonly>
                            </div>

                            <!-- DISC -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">DISC %</label>
                                <input type="text" class="form-control form-control-sm text-end" id="diskon_invoice_edit_ren">
                            </div>

                            <!-- TOTAL -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">TOTAL</label>
                                <input type="text" class="form-control form-control-sm text-end bg-secondary-subtle" id="total_invoice_edit_ren" readonly>
                            </div>

                            <!-- DEL CHARGE -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">DEL.CHARGE</label>
                                <input type="text" class="form-control form-control-sm text-end" id="del_charge_invoice_edit_ren">
                            </div>

                            <!-- PPN -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">PPN %</label>
                                <input type="text" class="form-control form-control-sm text-end" id="ppn_invoice_edit_ren">
                            </div>

                            <!-- GRAND -->
                            <div class="col-md-4">
                                <label class="fw-bold small mb-0 d-block">GRAND</label>
                                <input type="text" class="form-control form-control-sm text-end fw-bold bg-danger-subtle" id="grand_invoice_edit_ren">
                            </div>

                            <!-- KETERANGAN -->
                            <div class="col-md-12">
                                <label class="fw-bold small mb-0 d-block">KETERANGAN</label>
                                <textarea class="form-control form-control-sm" rows="2" id="keterangan_invoice_edit_ren"></textarea>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

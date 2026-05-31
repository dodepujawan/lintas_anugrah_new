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

                {{-- <div class="col-lg-3">
                    <label class="form-label mb-1">CARI</label>
                    <input type="text"
                        class="form-control form-control-sm"
                        id="search_invoice_ren"
                        placeholder="Invoice / Customer">
                </div> --}}

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
<script>
$(document).ready(function() {
    // Set CSRF token in AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
// ================================= Tabel Edit Dingin =====================================
    let tableInvoiceRen = $('#table_invoice_ren').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: false,
        ajax: {
            url: "{{ route('rentPendinginInvGen.tableEdit') }}",
            data: function(d) {
                d.tanggal_dari = $('#tanggal_dari_invoice_ren').val();
                d.tanggal_sampai = $('#tanggal_sampai_invoice_ren').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
            { data: 'TGLINVOICE', name: 'TGLINVOICE' },
            { data: 'INVOICE', name: 'INVOICE' },
            { data: 'CUSTOMER', name: 'CUSTOMER' },
            { data: 'GRAND', name: 'GRAND', className: 'text-end' },
            { data: 'PIUTANG', name: 'PIUTANG', className: 'text-end' },
            { data: 'bayar', name: 'bayar', searchable: false, className: 'text-end' },
            { data: 'aksi', name: 'aksi', searchable: false, orderable: false }
        ]
    });
    // Reload Filter
    $('#btn_filter_invoice_ren').click(function () {
        tableInvoiceRen.ajax.reload();
    });
// ============================== End Of Tabel dit Dingin ===================================
// ================================== Show Edit Dingin ======================================
    $(document).on('click', '.btn_edit_invoice_ren', function() {
        let invoice = $(this).data('invoice');
        $.ajax({
            url: "{{ route('rentPendinginInvGen.showEdit', ':invoice') }}".replace(':invoice', invoice),
            type: 'GET',
            success: function(res) {
                if (!res.status) {
                    Swal.fire('Error', res.message, 'error');
                    return;
                }
                clearFormInvoiceEditRen();
                let m = res.master;
                $('#invoice_invoice_edit_ren').val(m.invoice);
                $('#customer_invoice_edit_ren').val(m.customer);
                $('#kendaraan_invoice_edit_ren').val(m.kendaraan);
                $('#driver_invoice_edit_ren').val(m.driver);
                $('#nomuat_invoice_edit_ren').val(m.nomuat);
                $('#tgl_invoice_invoice_edit_ren').val(m.tgl_invoice.substring(0,10));
                $('#tgl_jt_invoice_edit_ren').val(m.tgl_jt.substring(0,10));
                $('#bayar_invoice_edit_ren').val(formatRupiah(m.bayar));
                $('#piutang_invoice_edit_ren').val(formatRupiah(m.piutang));
                $('#rute_invoice_edit_ren').val(m.rute);
                $('#jumlah_invoice_edit_ren').val(parseFloat(m.jumlah));
                $('#harga_invoice_edit_ren').val(formatRupiah(m.harga));
                $('#subtotal_invoice_edit_ren').val(formatRupiah(m.subtotal));
                $('#diskon_invoice_edit_ren').val(parseFloat(m.diskon));
                $('#total_invoice_edit_ren').val(formatRupiah(m.total));
                $('#del_charge_invoice_edit_ren').val(formatRupiah(m.del_charge));
                $('#ppn_invoice_edit_ren').val(parseFloat(m.ppn));
                $('#grand_invoice_edit_ren').val(formatRupiah(m.grand));
                $('#keterangan_invoice_edit_ren').val(m.keterangan);
                $('#modal_invoice_edit_ren').modal('show');
            }
        });
    });
// ============================== End Of Show Edit Dingin ===================================
// =================================== Hitung Edit Dingin =====================================
$(document).on('keyup','#jumlah_invoice_edit_ren,#harga_invoice_edit_ren,#diskon_invoice_edit_ren,#del_charge_invoice_edit_ren,#ppn_invoice_edit_ren,#bayar_invoice_edit_ren',
    function () {
        hitungInvoiceEditRen();
    }
);
// ============================== End Of Hitung Edit Dingin ===================================
// ================================== Submit Edit Dingin ======================================
    $('#btn_simpan_invoice_edit_ren').click(function() {
        $.ajax({
            url: "{{ route('rentPendinginInvGen.updateEdit') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                invoice: $('#invoice_invoice_edit_ren').val(),
                tgl_jt: $('#tgl_jt_invoice_edit_ren').val(),
                jumlah: $('#jumlah_invoice_edit_ren').val(),
                harga: unformatRupiah($('#harga_invoice_edit_ren').val()),
                disc: $('#diskon_invoice_edit_ren').val(),
                del_charge: unformatRupiah($('#del_charge_invoice_edit_ren').val()),
                ppn: $('#ppn_invoice_edit_ren').val(),
                keterangan: $('#keterangan_invoice_edit_ren').val()
            },
            beforeSend: function() { $('#btn_simpan_invoice_edit_ren').prop('disabled', true); },
            success: function(res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modal_invoice_edit_ren').modal('hide');
                tableInvoiceRen.ajax.reload(null, false);
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message ?? 'Terjadi kesalahan', 'error');
            },
            complete: function() { $('#btn_simpan_invoice_edit_ren').prop('disabled', false); }
        });
    });
// ============================== End Of Submit Edit Dingin ===================================
});
// ########################################################################
// FUNCTION HELPER:
// ########################################################################
// Format Angka
function formatRupiah(angka) {
    if (!angka) return 0;

    return parseFloat(angka)
        .toLocaleString('id-ID');
}
// Unformat Angka
function unformatRupiah(angka) {

    if (!angka) return 0;

    return parseInt(
        angka.toString().replace(/\./g, '')
    ) || 0;
}

// Fungsi Hitung
function hitungInvoiceEditRen() {
    let jumlah = parseFloat($('#jumlah_invoice_edit_ren').val()) || 0;
    let harga = unformatRupiah($('#harga_invoice_edit_ren').val());
    let disc = parseFloat($('#diskon_invoice_edit_ren').val()) || 0;
    let delCharge = unformatRupiah($('#del_charge_invoice_edit_ren').val());
    let ppnPersen = parseFloat($('#ppn_invoice_edit_ren').val()) || 0;
    let bayar = unformatRupiah($('#bayar_invoice_edit_ren').val());

    let subTotal = jumlah * harga;
    let discAmount = subTotal * (disc / 100);
    let total = subTotal - discAmount;
    let ppnNominal = total * (ppnPersen / 100);
    let grand = total + ppnNominal + delCharge;
    let piutang = grand - bayar;

    $('#subtotal_invoice_edit_ren').val(formatRupiah(Math.round(subTotal)));
    $('#total_invoice_edit_ren').val(formatRupiah(Math.round(total)));
    $('#grand_invoice_edit_ren').val(formatRupiah(Math.round(grand)));
    $('#piutang_invoice_edit_ren').val(formatRupiah(Math.round(piutang)));
}

function clearFormInvoiceEditRen()
{
    // =====================================
    // IDENTITAS
    // =====================================
    $('#invoice_invoice_edit_ren').val('');
    $('#customer_invoice_edit_ren').val('');

    $('#kendaraan_invoice_edit_ren').val('');
    $('#driver_invoice_edit_ren').val('');
    $('#nomuat_invoice_edit_ren').val('');

    // =====================================
    // TANGGAL
    // =====================================
    $('#tgl_invoice_invoice_edit_ren').val('');
    $('#tgl_jt_invoice_edit_ren').val('');

    // =====================================
    // RUTE
    // =====================================
    $('#rute_invoice_edit_ren').val('');

    // =====================================
    // NILAI
    // =====================================
    $('#jumlah_invoice_edit_ren').val('');

    $('#harga_invoice_edit_ren').val('');
    $('#subtotal_invoice_edit_ren').val('');
    $('#total_invoice_edit_ren').val('');

    $('#diskon_invoice_edit_ren').val('');
    $('#del_charge_invoice_edit_ren').val('');

    $('#ppn_invoice_edit_ren').val('');
    $('#grand_invoice_edit_ren').val('');

    // =====================================
    // PEMBAYARAN
    // =====================================
    $('#bayar_invoice_edit_ren').val('');
    $('#piutang_invoice_edit_ren').val('');

    // =====================================
    // KETERANGAN
    // =====================================
    $('#keterangan_invoice_edit_ren').val('');
}
</script>

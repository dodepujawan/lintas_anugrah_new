{{-- Bagian Tabel Expedisi Kwitansi --}}
<div class="container mt-3" id="table_kwt_exp">
    <div class="card p-3">
        <h5 class="text-center mb-3">FORM PROSES INVOICE EXPEDISI</h5>
        <div class="table-responsive">
            <table id="ExpKwtTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Grand Total</th>
                        <th>Piutang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
{{-- Bagian Form Expedisi Kwitansi --}}
<div class="container mt-3 d-none" id="form_kwt_exp">
    <div class="card p-3" style="background-color:#b7e1b0;">
        <h5 class="text-center mb-3">FORM PROSES INVOICE EXPEDISI</h5>
        <div class="row">
            <!-- LEFT SIDE -->
            <div class="col-md-5">

                <div class="mb-2">
                    <label>SUB TOTAL</label>
                    <input type="text" id="sub_total_kwt_exp" class="form-control text-end" readonly>
                </div>

                <div class="mb-2">
                    <label>D.CHARGE</label>
                    <input type="text" id="d_charge_kwt_exp" class="form-control text-end" readonly>
                </div>

                <div class="mb-2">
                    <label>TOTAL</label>
                    <input type="text" id="total_kwt_exp" class="form-control text-end" readonly>
                </div>

                <div class="mb-2 row">
                    <div class="col-6">
                        <label>DISC %</label>
                        <input type="text" id="disc_persen_kwt_exp" class="form-control text-end" readonly>
                    </div>
                    <div class="col-6">
                        <label>DISC Rp</label>
                        <input type="text" id="disc_rp_kwt_exp" class="form-control text-end" readonly>
                    </div>
                </div>

                <div class="mb-2">
                    <label>DPP</label>
                    <input type="text" id="dpp_kwt_exp" class="form-control text-end" readonly>
                </div>

                <div class="mb-2 row">
                    <div class="col-6">
                        <label>PPN %</label>
                        <input type="text" id="ppn_persen_kwt_exp" class="form-control text-end" readonly>
                    </div>
                    {{-- <div class="col-6">
                        <label>PPN Rp</label>
                        <input type="text" id="ppn_rp_kwt_exp" class="form-control text-end" >
                    </div> --}}
                </div>

                <div class="mb-2">
                    <label>GRAND</label>
                    <input type="text" id="grand_kwt_exp" class="form-control text-end" readonly>
                </div>

                <div class="mb-2">
                    <label>TGL INVOICE</label>
                    <input type="date" id="tgl_invoice_kwt_exp" class="form-control" readonly>
                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-md-7">

                <div class="mb-2">
                    <label>NO FAKTUR</label>
                    <input type="text" id="no_faktur_kwt_exp" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>NOMOR MUAT</label>
                    <input type="text" id="nomor_muat_kwt_exp" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>NOMOR SJ</label>
                    <input type="text" id="nomor_sj_kwt_exp" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>KENDARAAN</label>
                    <input type="text" id="kendaraan_kwt_exp" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>CUSTOMER</label>
                    <input type="text" id="customer_kwt_exp" class="form-control" readonly>
                </div>

            </div>
        </div>

        <hr>

        <!-- BAYAR SECTION -->
        <div class="card p-3 mt-3" style="background-color:#e6e6e6;">
            <div class="row">
                <div class="col-md-3">
                    <label>BAYAR</label>
                    <input type="text" id="bayar_kwt_exp" class="form-control text-end">
                </div>

                <div class="col-md-2">
                    <label>TOP (HARI)</label>
                    <input type="number" id="top_kwt_exp" class="form-control" >
                </div>

                <div class="col-md-3">
                    <label>TGL JTP</label>
                    <input type="date" id="tgl_jtp_kwt_exp" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>PIUTANG</label>
                    <input type="text" id="piutang_kwt_exp" class="form-control text-end"  readonly>
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-primary" id="proses_kwt_exp">PROSES</button>
                <button class="btn btn-secondary" id="keluar_kwt_exp">KELUAR</button>
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
// ================================= Tabel Kwitansi Expedisi =====================================
    if ($.fn.DataTable.isDataTable('#ExpKwtTable')) {
        $('#ExpKwtTable').DataTable().destroy();
    }
    $('#ExpKwtTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('expedisiKwitansi.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'INVOICE', name: 'INVOICE' },
            { data: 'TGLINVOICE', name: 'TGLINVOICE' },
            { data: 'CUSTOMER', name: 'CUSTOMER' },
            { data: 'GRAND', name: 'GRAND', className: 'text-end' },
            { data: 'PIUTANG', name: 'PIUTANG', className: 'text-end' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
// ============================== End Of Tabel Kwitansi Expedisi ==================================
// =============================== Form Detail Kwitansi Expedisi ===================================
    $(document).on('click', '.btn-show-invoice-kwt', function() {

        let invoiceNo = $(this).data('invoice');

        // bisa ajax ambil detail invoice juga kalau mau
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            $.ajax({
                url: "{{ route('expedisiKwitansi.show', ':kode') }}".replace(':kode', invoiceNo),
                type: "GET",
                success: function(response) {

                    if (!response.status) {
                        alert(response.message);
                        return;
                    }
                    $('#loading_modal').modal('hide');
                    $('#table_kwt_exp').addClass('d-none');
                    $('#form_kwt_exp').removeClass('d-none');
                    let d = response.data;

                    // ===== LEFT SIDE =====
                    $('#sub_total_kwt_exp').val(formatRupiah(d.sub_total));
                    $('#d_charge_kwt_exp').val(formatRupiah(d.d_charge));
                    $('#total_kwt_exp').val(formatRupiah(d.total));

                    $('#disc_persen_kwt_exp').val(d.disc_persen);
                    $('#disc_rp_kwt_exp').val(formatRupiah(d.disc_rp));

                    $('#dpp_kwt_exp').val(formatRupiah(d.total - d.disc_rp));
                    $('#ppn_persen_kwt_exp').val(formatRupiah(d.ppn));
                    $('#grand_kwt_exp').val(formatRupiah(d.grand));

                    $('#tgl_invoice_kwt_exp').val(
                        d.tgl_invoice.substring(0,10)
                    );

                    // ===== RIGHT SIDE =====
                    $('#no_faktur_kwt_exp').val(d.invoice);
                    $('#nomor_muat_kwt_exp').val(d.nomor_muat);
                    $('#nomor_sj_kwt_exp').val(d.nomor_sj);
                    $('#kendaraan_kwt_exp').val(d.kendaraan);
                    $('#customer_kwt_exp').val(d.customer);

                    // ===== BAYAR SECTION =====
                    $('#bayar_kwt_exp').val(0);
                    $('#piutang_kwt_exp').val(formatRupiah(d.piutang));

                    $('#modalKwitansiExp').modal('show');
                },
                error: function(xhr) {
                    $('#loading_modal').modal('hide');
                    alert('Terjadi kesalahan server');
                }
            });
        });
    });

// ============================ End Of Form Detail Kwitansi Expedisi ===============================
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

</script>

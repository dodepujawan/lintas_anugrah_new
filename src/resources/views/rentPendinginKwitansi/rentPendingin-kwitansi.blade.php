{{-- Bagian Tabel Dingin Kwitansi --}}
<div class="container mt-3" id="table_kwt_dgn">
    <div class="card p-3">
        <h5 class="text-center mb-3">FORM PROSES INVOICE MOBIL PENDINGIN</h5>
        <div class="mb-3">
            <label>
                <input type="radio" name="filter_kwt_dgn" value="belum" checked>
                Belum Kwitansi
            </label>
            <label class="ms-3">
                <input type="radio" name="filter_kwt_dgn" value="sudah">
                Sudah Kwitansi
            </label>
        </div>
        <div class="table-responsive">
            <table id="DgnKwtTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Invoice</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Grand Total</th>
                        <th>No Kwitansi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
{{-- Bagian Form Dingin Kwitansi --}}
<div class="container mt-3 d-none" id="form_kwt_dgn">
    <div class="card p-3" style="background-color:#b7e1b0;">
        <h5 class="text-center mb-3">FORM PROSES INVOICE MOBIL PENDINGIN</h5>
        <div class="row">
            <!-- LEFT SIDE -->
            <div class="col-md-5">
                <input type="hidden" id="kwt_exp_flag_dgn">
                <div class="mb-2">
                    <label>SUB TOTAL</label>
                    <input type="text" id="sub_total_kwt_dgn" class="form-control text-end" readonly>
                </div>

                <div class="mb-2">
                    <label>D.CHARGE</label>
                    <input type="text" id="d_charge_kwt_dgn" class="form-control text-end" readonly>
                </div>

                <div class="mb-2">
                    <label>TOTAL</label>
                    <input type="text" id="total_kwt_dgn" class="form-control text-end" readonly>
                </div>

                <div class="mb-2 row">
                    <div class="col-6">
                        <label>DISC %</label>
                        <input type="text" id="disc_persen_kwt_dgn" class="form-control text-end" readonly>
                    </div>
                    <div class="col-6">
                        <label>DISC Rp</label>
                        <input type="text" id="disc_rp_kwt_dgn" class="form-control text-end" readonly>
                    </div>
                </div>

                <div class="mb-2">
                    <label>DPP</label>
                    <input type="text" id="dpp_kwt_dgn" class="form-control text-end" readonly>
                </div>

                <div class="mb-2 row">
                    <div class="col-6">
                        <label>PPN %</label>
                        <input type="text" id="ppn_persen_kwt_dgn" class="form-control text-end" readonly>
                    </div>
                    {{-- <div class="col-6">
                        <label>PPN Rp</label>
                        <input type="text" id="ppn_rp_kwt_exp" class="form-control text-end" >
                    </div> --}}
                </div>

                <div class="mb-2">
                    <label>GRAND</label>
                    <input type="text" id="grand_kwt_dgn" class="form-control text-end" readonly>
                </div>

                <div class="mb-2">
                    <label>TGL INVOICE</label>
                    <input type="date" id="tgl_invoice_kwt_dgn" class="form-control" readonly>
                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-md-7">

                <div class="mb-2">
                    <label>NO FAKTUR</label>
                    <input type="text" id="no_faktur_kwt_dgn" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>NOMOR MUAT</label>
                    <input type="text" id="nomor_muat_kwt_dgn" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>NOMOR SJ</label>
                    <input type="text" id="nomor_sj_kwt_dgn" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>KENDARAAN</label>
                    <input type="text" id="kendaraan_kwt_dgn" class="form-control" readonly>
                </div>

                <div class="mb-2">
                    <label>CUSTOMER</label>
                    <input type="text" id="customer_kwt_dgn" class="form-control" readonly>
                </div>

            </div>
        </div>

        <hr>
        <div id="edit_mode_alert_dgn"
            class="alert alert-warning text-center fw-bold"
            style="display: none; font-size: 16px;">
            ⚠ MODE EDIT KWITANSI !
        </div>
        <!-- BAYAR SECTION -->
        <div class="card p-3 mt-3" style="background-color:#e6e6e6;">
            <div class="row">
                <div class="col-md-3">
                    <label>BAYAR</label>
                    <input type="text" id="bayar_kwt_dgn" class="form-control text-end">
                </div>

                <div class="col-md-2">
                    <label>TOP (HARI)</label>
                    <input type="number" id="top_kwt_dgn" class="form-control" >
                </div>

                <div class="col-md-3">
                    <label>TGL JTP</label>
                    <input type="date" id="tgl_jtp_kwt_dgn" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>PIUTANG</label>
                    <input type="text" id="piutang_kwt_dgn" class="form-control text-end"  readonly>
                </div>
            </div>

            <div class="text-end mt-3">
                <button class="btn btn-primary" id="proses_kwt_dgn">PROSES</button>
                <button class="btn btn-secondary" id="keluar_kwt_dgn">KELUAR</button>
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
// ================================= Tabel Kwitansi Dingin =====================================
    if ($.fn.DataTable.isDataTable('#DgnKwtTable')) {
        $('#DgnKwtTable').DataTable().destroy();
    }
    let table_kwt_dgn = $('#DgnKwtTable').DataTable({
        processing: true,
        serverSide: true,
        ajax:{
            url: "{{ route('rentPendinginKwitansi.data') }}",
            data: function (d) {
                d.status_kwt = $('input[name="filter_kwt_dgn"]:checked').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'INVOICE', name: 'INVOICE' },
            { data: 'TGLINVOICE', name: 'TGLINVOICE' },
            { data: 'CUSTOMER', name: 'CUSTOMER' },
            { data: 'GRAND', name: 'GRAND', className: 'text-end' },
            // { data: 'PIUTANG', name: 'PIUTANG', className: 'text-end' },
            { data: 'no_kwt', name: 'kwt', visible: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Mengubah Radio Status Kwitansi
    $('input[name="filter_kwt_dgn"]').on('change', function() {

        let status = $(this).val();

        if(status === 'sudah'){
            table_kwt_dgn.column(5).visible(true); // tampilkan kolom No Kwitansi
        } else {
            table_kwt_dgn.column(5).visible(false);
        }

        table_kwt_dgn.ajax.reload();
    });
// ============================== End Of Tabel Kwitansi Dingin ==================================
// =============================== Form Detail Kwitansi Dingin ===================================
    $(document).on('click', '.btn-show-invoice-dgn-kwt', function() {

        let invoiceNo = $(this).data('invoice');

        // bisa ajax ambil detail invoice juga kalau mau
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            $.ajax({
                url: "{{ route('rentPendinginKwitansi.show', ':kode') }}".replace(':kode', invoiceNo),
                type: "GET",
                success: function(response) {

                    if (!response.status) {
                        alert(response.message);
                        return;
                    }
                    $('#loading_modal').modal('hide');
                    $('#table_kwt_dgn').addClass('d-none');
                    $('#form_kwt_dgn').removeClass('d-none');
                    // Clear Form Dulu
                    clearAllKwtDgn();

                    let d = response.data;
                    // ===== LEFT SIDE =====
                    $('#sub_total_kwt_dgn').val(formatRupiah(d.sub_total));
                    $('#d_charge_kwt_dgn').val(formatRupiah(d.d_charge));
                    $('#total_kwt_dgn').val(formatRupiah(d.total));

                    $('#disc_persen_kwt_dgn').val(d.disc_persen);
                    $('#disc_rp_kwt_dgn').val(formatRupiah(d.disc_rp));

                    $('#dpp_kwt_dgn').val(formatRupiah(d.total - d.disc_rp));
                    $('#ppn_persen_kwt_dgn').val(formatRupiah(d.ppn));
                    $('#grand_kwt_dgn').val(formatRupiah(d.grand));

                    $('#tgl_invoice_kwt_dgn').val(
                        d.tgl_invoice.substring(0,10)
                    );

                    // ===== RIGHT SIDE =====
                    $('#no_faktur_kwt_dgn').val(d.invoice);
                    $('#nomor_muat_kwt_dgn').val(d.nomor_muat);
                    $('#nomor_sj_kwt_dgn').val(d.nomor_sj);
                    $('#kendaraan_kwt_dgn').val(d.kendaraan);
                    $('#customer_kwt_dgn').val(d.customer);

                    // ===== BAYAR SECTION =====
                    $('#kwt_exp_flag_dgn').val(0);
                    $('#bayar_kwt_dgn').val(0);
                    $('#top_kwt_dgn').val(0);
                    $('#piutang_kwt_dgn').val(formatRupiah(d.piutang));
                },
                error: function(xhr) {
                    $('#loading_modal').modal('hide');
                    alert('Terjadi kesalahan server');
                }
            });
        });
    });

// ============================ End Of Form Detail Kwitansi Dingin ===============================
// =============================== Submit Kwitansi Dingin ===================================
    $('#proses_kwt_dgn').on('click', function() {
        let bayar = parseFloat($('#bayar_kwt_dgn').val());
        let top   = parseFloat($('#top_kwt_dgn').val());

        // Validasi BAYAR
        if (isNaN(bayar) || bayar <= 0) {
            alert('Nominal bayar harus angka dan lebih dari 0');
            $('#bayar_kwt_dgn').focus();
            return false;
        }

        // Validasi TOP
        if (isNaN(top) || top < 0) {
            alert('TOP harus angka dan tidak boleh negatif');
            $('#top_kwt_dgn').focus();
            return false;
        }

        let data = {
            invoice: $('#no_faktur_kwt_dgn').val(),
            bayar: $('#bayar_kwt_dgn').val(),
            top: $('#top_kwt_dgn').val(),
            tgl_jtp: $('#tgl_jtp_kwt_dgn').val()
        };
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            $.ajax({
                url: "{{ route('rentPendinginKwitansi.store') }}",
                type: "POST",
                data: data,
                success: function(response) {

                    if (response.status) {
                        $('#loading_modal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#form_kwt_dgn').addClass('d-none');
                        $('#table_kwt_dgn').removeClass('d-none');
                        $('#DgnKwtTable').DataTable().ajax.reload();
                        // Cetak PDF
                        window.open(response.redirect, '_blank');
                    } else {
                        $('#loading_modal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }

                },
                error: function(xhr) {
                    $('#loading_modal').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi Kesalahan Server !'
                    });
                }
            });
        });

    });
// ============================ End Of Submit Kwitansi Dingin ===============================
// ============================== Delete Kwitansi ================================
    $(document).on('click', '.btn-hapus-kwt-dgn', function() {
        var invoice = $(this).data('invoice');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Kwitansi Akan Dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading_modal').modal('show');
                $('#loading_modal').one('shown.bs.modal', function () {
                    $.ajax({
                        url: "{{ route('rentPendinginKwitansi.destroy') }}",
                        type: 'POST',
                        data: {
                            invoice: invoice
                        },
                        success: function(response) {
                            $('#loading_modal').modal('hide');
                            Swal.fire('Terhapus!', response.success, 'success');
                            $('#DgnKwtTable').DataTable().ajax.reload();
                        }
                    });
                });
            }
        });
    });
// ============================== End Of Delete Kwitansi ================================
// ============================= Form Detail Edit Kwitansi Dingin ===============================
    $(document).on('click', '.btn-edit-kwt-dgn', function() {

        let invoiceNo = $(this).data('invoice');

        // bisa ajax ambil detail invoice juga kalau mau
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            $.ajax({
                url: "{{ route('rentPendinginKwitansi.show', ':kode') }}".replace(':kode', invoiceNo),
                type: "GET",
                success: function(response) {

                    if (!response.status) {
                        alert(response.message);
                        return;
                    }
                    $('#loading_modal').modal('hide');
                    $('#table_kwt_dgn').addClass('d-none');
                    $('#form_kwt_dgn').removeClass('d-none');
                    // Clear Form Dulu
                    clearAllKwtDgn();

                    let d = response.data;
                    // ===== LEFT SIDE =====
                    $('#sub_total_kwt_dgn').val(formatRupiah(d.sub_total));
                    $('#d_charge_kwt_dgn').val(formatRupiah(d.d_charge));
                    $('#total_kwt_dgn').val(formatRupiah(d.total));

                    $('#disc_persen_kwt_dgn').val(d.disc_persen);
                    $('#disc_rp_kwt_dgn').val(formatRupiah(d.disc_rp));

                    $('#dpp_kwt_dgn').val(formatRupiah(d.total - d.disc_rp));
                    $('#ppn_persen_kwt_dgn').val(formatRupiah(d.ppn));
                    $('#grand_kwt_dgn').val(formatRupiah(d.grand));

                    $('#tgl_invoice_kwt_dgn').val(
                        d.tgl_invoice.substring(0,10)
                    );

                    // ===== RIGHT SIDE =====
                    $('#no_faktur_kwt_dgn').val(d.invoice);
                    $('#nomor_muat_kwt_dgn').val(d.nomor_muat);
                    $('#nomor_sj_kwt_dgn').val(d.nomor_sj);
                    $('#kendaraan_kwt_dgn').val(d.kendaraan);
                    $('#customer_kwt_dgn').val(d.customer);

                    // ===== BAYAR SECTION =====
                    $('#edit_mode_alert_dgn').fadeIn();
                    $('#kwt_exp_flag_dgn').val(1);
                    $('#bayar_kwt_dgn').val(formatRupiah(d.bayar));
                    $('#top_kwt_dgn').val(formatRupiah(d.saldo));
                    $('#tgl_jtp_kwt_dgn').val(d.tgl_jt);
                    $('#piutang_kwt_dgn').val(formatRupiah(d.piutang));
                },
                error: function(xhr) {
                    $('#loading_modal').modal('hide');
                    alert('Terjadi kesalahan server');
                }
            });
        });
    });

// ========================= End Of Form Detail Edit Kwitansi Dingin ==============================
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
// Clear Form
function clearAllKwtDgn() {
    $('#edit_mode_alert_dgn').fadeOut();
    // ===== LEFT SIDE =====
    $('#sub_total_kwt_dgn').val('');
    $('#d_charge_kwt_dgn').val('');
    $('#total_kwt_dgn').val('');
    $('#disc_persen_kwt_dgn').val('');
    $('#disc_rp_kwt_dgn').val('');
    $('#dpp_kwt_dgn').val('');
    $('#ppn_persen_kwt_dgn').val('');
    $('#grand_kwt_dgn').val('');
    $('#tgl_invoice_kwt_dgn').val('');

    // ===== RIGHT SIDE =====
    $('#no_faktur_kwt_dgn').val('');
    $('#nomor_muat_kwt_dgn').val('');
    $('#nomor_sj_kwt_dgn').val('');
    $('#kendaraan_kwt_dgn').val('');
    $('#customer_kwt_dgn').val('');

    // ===== BAYAR SECTION =====
    $('#kwt_exp_flag_dgn').val('');
    $('#bayar_kwt_dgn').val('');
    $('#top_kwt_dgn').val('');
    $('#piutang_kwt_dgn').val('');

    // set ulang tanggal hari ini
    $('#tgl_jtp_kwt_dgn').val(new Date().toISOString().split('T')[0]);
}
</script>

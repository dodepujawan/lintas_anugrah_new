<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-3">
                <select id="filter_status_kwt_coolroom" class="form-control">
                    <option value="belum">Belum Kwitansi</option>
                    <option value="sudah">Sudah Kwitansi</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="kwitansiCoolroomTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Tgl Invoice</th>
                        <th>Customer</th>
                        <th>Grand</th>
                        <th>Piutang</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
// ============================= Tabel Invoice Generate Coolroom =================================
    let table = $('#kwitansiCoolroomTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('coolroomKwt.getData') }}",
            data: function (d) {
                d.status_kwt = $('#filter_status_kwt_coolroom').val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { data: 'INVOICE', name: 'INVOICE' },
            { data: 'TGLINVOICE', name: 'TGLINVOICE' },
            { data: 'CUSTOMER', name: 'CUSTOMER' },
            { data: 'GRAND', name: 'GRAND' },
            { data: 'PIUTANG', name: 'PIUTANG' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    // reload filter
    $('#filter_status_kwt_coolroom').change(function () {
        table.ajax.reload();
    });
// ========================== End Of Tabel Invoice Generate Coolroom ===============================
// ================================ Kwitansi Generate Coolroom ===================================
    $(document).on('click', '.btn-proses-kwt-coolroom', function () {
            let invoice = $(this).data('invoice');
            Swal.fire({
                title: 'Proses Kwitansi?',
                text: invoice,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya Proses',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('coolroomKwt.proses') }}",
                        type: "POST",
                        data: {
                            invoice: invoice,
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Loading...',
                                text: 'Sedang memproses kwitansi',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            // Pdf
                            if (response.pdf_url) {window.open(response.pdf_url, '_blank');}
                            $('#kwitansiCoolroomTable').DataTable().ajax.reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message
                                    ?? 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        }
    );
// ========================== End Of Tabel Kwitansi Generate Coolroom ===============================
// =================================== Kwitansi Delete Coolroom ====================================
    $(document).on('click', '.btn-delete-kwt-coolroom',function () {
            let kwt = $(this).data('kwt');
            Swal.fire({
                title: 'Delete Kwitansi?',
                text: kwt,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya Delete',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('coolroomKwt.delete') }}",
                        type: "POST",
                        data: {
                            kwt: kwt,
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Loading...',
                                text: 'Sedang delete kwitansi',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            $('#kwitansiCoolroomTable')
                                .DataTable()
                                .ajax
                                .reload(null, false);
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message
                                    ?? 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        }
    );
// ================================ End Of Kwitansi Delete Coolroom ===================================
});
</script>

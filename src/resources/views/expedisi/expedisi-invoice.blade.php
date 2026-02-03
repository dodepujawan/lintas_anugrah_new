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
    /*  */
    table#InvoiceExpTable.table-striped > tbody > tr.selected > * {
        background-color: #cfe2ff !important;
    }

    table#InvoiceExpTable tbody tr:hover {
        cursor: pointer;
    }
</style>
<div class="container-fluid mt-3">
    <!-- Header Form -->
    <div class="card-expedisi">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-truck me-2'></i>FORM EXPEDISI</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label d-block">Filter Invoice</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_invoice" id="radio_belum_invoice" value="belum" checked>
                        <label class="form-check-label" for="radio_belum_invoice">
                            Belum Invoice
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_invoice" id="radio_semua_invoice" value="semua">
                        <label class="form-check-label" for="radio_semua_invoice">
                            Semua
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" class="form-control form-control-sm" id="filter_tgl_mulai">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Akhir</label>
                    <input type="date" class="form-control form-control-sm" id="filter_tgl_akhir">
                </div>
                <div class="col-md-3">
                    <label>Filter Data</label>
                    <input type="text" class="form-control form-control-sm" id="filter_invoice_expedisi">
                </div>
                <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div>
                            <button class="btn btn-sm btn-info" id="btn_filter_invoice_expedisi">
                                <i class='bx bx-filter'></i> Filter
                            </button>
                        </div>
                    </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="InvoiceExpTable">
                    <thead>
                    <tr>
                        <th width="30">No</th>
                        <th>NO MUAT</th>
                        <th>TGL MUAT</th>
                        <th>CUSTOMER</th>
                        <th>RUTE</th>
                        <th>JUMLAH</th>
                        <th>HARGA</th>
                        <th>DISC</th>
                        <th>DEL CHARGE</th>
                        <th>TOTAL</th>
                        <th>NO SJ</th>
                        <th width="120">AKSI</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
    // ================================= Pilih No Muat =====================================
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#InvoiceExpTable')) {
            $('#InvoiceExpTable').DataTable().destroy();
        }
        var tableInvoiceExp = $('#InvoiceExpTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url: "{{ route('expedisiInvoice.data') }}",
                data: function(d) {
                    d.tgl_mulai = $('#filter_tgl_mulai').val();
                    d.tgl_akhir = $('#filter_tgl_akhir').val();
                    d.search_muat = $('#filter_muat').val();
                    d.filter_invoice = $('input[name="filter_invoice"]:checked').val();
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

        $('#btn_filter_invoice_expedisi').click(function () {
            tableInvoiceExp.ajax.reload();
        });
    // ============================= End Of Pilih No Muat =====================================
    // ============================= Highlight No Muat Table =====================================
    $('#InvoiceExpTable tbody').on('click', 'tr', function () {
        // Abaikan child row (kalau responsive aktif)
        if ($(this).hasClass('child')) return;
        if ($(this).hasClass('selected')) {
            // klik ulang → unselect
            $(this).removeClass('selected');
        } else {
            // hapus selection lain
            $('#InvoiceExpTable tbody tr.selected').removeClass('selected');
            // select baris ini
            $(this).addClass('selected');
        }
    });
    // ========================== End Of Highlight No Muat Table ==================================
});
</script>


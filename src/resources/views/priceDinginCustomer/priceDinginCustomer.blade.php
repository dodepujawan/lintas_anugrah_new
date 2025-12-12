<style>
    .card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
    }

    .card-prices-customer-dingin-header {
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-prices-customer-header h5 {
        color: #007bff;
        margin: 0;
    }

    /* Untuk mobile */
    @media (max-width: 576px) {
        .card-prices-customer-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .card-prices-customer-header > div:last-child {
            align-self: flex-end;
        }
    }

    /* Keterangan Customer */
    #customerInfo {
    background: #f1faff;
    border-left: 5px solid #0d6efd;
    border-radius: 6px;
    }
    #customerInfo span {
        font-size: 15px;
        color: #333;
    }
    #customerInfo strong {
        color: #0d6efd;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .btn {
        border-radius: 6px;
        font-weight: 500;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-prices-customer-dingin-header">
                    <div>
                        <h5>Harga Customer</h5>
                    </div>

                    <div class="card-body">
                    {{-- View Table Cust All --}}
                    <div class="table-responsive" id="priceCusDinginTableMaster">
                        <table class="table table-bordered table-striped" id="priceCusDinginTable">
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
                            <tbody>
                                <!-- Data akan di-load oleh DataTables -->
                            </tbody>
                        </table>
                    </div>

                    {{-- View Table Detail Cust --}}
                    <div id="priceCusTableDetMaster" style="display:none;">
                        <div id="customerInfo" class="card shadow-sm p-3" style="display:none;">
                            <h5 class="mb-3">
                                <i class="bx bx-user"></i> Informasi Customer
                            </h5>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <strong>Nama Customer:</strong><br>
                                    <span id="custName"></span>
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Kode Customer:</strong><br>
                                    <span id="custKode"></span>
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Jenis Usaha:</strong><br>
                                    <span id="jenisUsaha"></span>
                                </div>
                                <div class="col-6 mb-2">
                                    <strong>Nama Pemilik:</strong><br>
                                    <span id="pemilikNama"></span>
                                </div>
                                <div class="col-12">
                                    <strong>Alamat:</strong><br>
                                    <span id="alamat"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary" id="add_rute_customer">
                                        + Tambah Harga Rute
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table id="priceCusTableDet" class="table table-striped table-bordered w-100">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>NAMA ITEM</th>
                                                <th>DARI</th>
                                                <th>SAMPAI</th>
                                                <th>RUTE</th>
                                                <th>HARGA</th>
                                                <th>JENIS</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
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
    // =============================== Initialize DataTables ====================================
    var table = $('#priceCusDinginTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("price-customer.data") }}',
        // Scroll settings
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        // Responsive settings
        responsive: true,
        autoWidth: true,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'kode', name: 'kode' },
            { data: 'nama', name: 'nama' },
            { data: 'jenis_usaha', name: 'jenis_usaha' },
            { data: 'telepon', name: 'telepon' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    // ============================= End Of Initialize DataTables =================================
});
</script>

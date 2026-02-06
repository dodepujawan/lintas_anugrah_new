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
    /* Styling Highlight Table */
    table#InvoiceExpTable.table-striped > tbody > tr.selected > * {
        background-color: #cfe2ff !important;
    }

    table#InvoiceExpTable tbody tr:hover {
        cursor: pointer;
    }

    /* Styling Form Input Invoice */
    #InvoiceExpTable tbody tr {
        cursor: pointer;
    }

    #InvoiceExpTable tbody tr.selected {
        background-color: #cfe2ff !important;
    }

    .table-sm td {
        padding: 4px 6px;
    }

    .card-header {
        background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
    }

    /* Table Gabung Size */
    #GabungExpTableRight,
    #GabungExpTableLeft {
        font-size: 12px;
    }

    #GabungExpTableRight th,
    #GabungExpTableRight td,
    #GabungExpTableLeft th,
    #GabungExpTableLeft td {
        padding: 4px 6px !important;
        vertical-align: middle;
    }

</style>
<div class="container-fluid mt-3">
    <!-- Header Form -->
    <div class="card-expedisi" id="master_form_exp_inv">
        <div class="card-expedisi-header">
            <h5><i class='bx bx-truck me-2'></i>FORM EXPEDISI</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label d-block">Filter Invoice</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                            type="radio" name="filter_invoice" id="radio_belum" value="belum" checked>
                        <label class="form-check-label" for="radio_belum">
                            Belum Invoice
                        </label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filter_invoice" id="radio_semua" value="semua">
                        <label class="form-check-label" for="radio_semua">
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
                        <th>PESANAN AWAL</th>
                        <th>NO GABUNG</th>
                        <th>PESANAN GABUNG</th>
                        <th>RUTE</th>
                        <th>KENDARAAN</th>
                        <th>JUMLAH</th>
                        <th>HARGA</th>
                        <th>DISC</th>
                        <th>DEL CHARGE</th>
                        <th>TOTAL</th>
                        <th>NO SJ</th>
                        <th>NO INVOICE</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="col-md-3 col-sm-6 d-flex gap-2">
                <button class="btn btn-primary btn-action flex-fill w-100" id="gabungInvExpBtn">
                    <i class='bx bx-plus-circle me-1'></i>Gabung Surat Jalan
                </button>
            </div>
        </div>
    </div>

    {{-- Bagian Show Detail Invoice --}}
    <div class="d-none" id="form_gabung_inv_exp">
        <div class="row g-3">

            <!-- KIRI -->
            <div class="col-md-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">
                        DATA MUAT / INVOICE
                    </div>
                    <div class="card-body p-2">
                        <!-- FORM INPUT -->
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small">Nomor</label>
                                <input type="text" class="form-control form-control-sm" id="no_gabung_exp_inv" readonly>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small">Customer</label>
                                <input type="text" class="form-control form-control-sm" id="customer_gabung_exp_inv">
                            </div>

                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-sm" id="customer_kode_gabung_exp_inv">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">No. SJ</label>
                                <input type="text" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-8">
                            <label class="form-label small">Item</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="item_gabung_exp_inv" placeholder="Cari item...">
                                <button class="btn btn-primary" type="button" id="btnItemGabungExp">
                                    <i class="bx bx-search"></i>
                                </button>
                            </div>
                        </div>

                            <div class="col-md-4">
                                <label class="form-label small">Jumlah (KG)</label>
                                <input type="text" class="form-control form-control-sm text-end" id="jumlah_gabung_exp_inv">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Harga @</label>
                                <input type="text" class="form-control form-control-sm text-end" id="harga_gabung_exp_inv">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">DPP</label>
                                <input type="text" class="form-control form-control-sm text-end" id="dpp_gabung_exp_inv" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Sub Total</label>
                                <input type="text" class="form-control form-control-sm text-end" id="total_gabung_exp_inv" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">PPN</label>
                                <div class="input-group input-group-sm">
                                    {{-- <span class="input-group-text">11%</span> --}}
                                    <input type="text" class="form-control text-end" id="ppn_gabung_exp_inv" readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small">Grand Total</label>
                                <input type="text" class="form-control form-control-sm text-end" id="grand_total_gabung_exp_inv" readonly>
                            </div>
                        </div>

                        <!-- TABLE KECIL -->
                        <div class="table-responsive mt-3">
                            <table id="GabungExpTableLeft" class="table table-bordered table-striped table-sm w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>NO MUAT</th>
                                        <th>TGL MUAT</th>
                                        <th>NO SJ</th>
                                        <th>PESANAN</th>
                                        <th>RUTE</th>
                                        <th>KENDARAAN</th>
                                        <th>JUMLAH</th>
                                        <th>HARGA</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button class="btn btn-primary" id="gabungInvExpBtn">
                            <i class="bx bx-plus-circle me-1"></i>
                            Gabung Surat Jalan
                        </button>
                    </div>
                </div>
            </div>

            <!-- KANAN -->
            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-bold">
                        DATA SURAT JALAN
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped table-sm w-100" id="GabungExpTableRight">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NO MUAT</th>
                                    <th>TGL MUAT</th>
                                    <th>NO SJ</th>
                                    <th>PESANAN</th>
                                    <th>RUTE</th>
                                    <th>KENDARAAN</th>
                                    <th>JUMLAH</th>
                                    <th>HARGA</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button class="btn btn-success w-100" id="addToInvExpBtn">
                        <i class="bx bx-arrow-from-right"></i> Tambahkan ke Invoice
                    </button>
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
    // ================================= Pilih No Muat =====================================
        // hancurkan datatable jika sudah pernah dipakai
        if ($.fn.DataTable.isDataTable('#InvoiceExpTable')) {
            $('#InvoiceExpTable').DataTable().destroy();
        }
        // untuk kepentingan ambil nilai CustomerKode saat di highlights
        let selectedRowData = null;
        var tableInvoiceExp = $('#InvoiceExpTable').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
            url: "{{ route('expedisiInvoice.data') }}",
                data: function(d) {
                    d.tgl_mulai = $('#filter_tgl_mulai').val();
                    d.tgl_akhir = $('#filter_tgl_akhir').val();
                    d.search_muat = $('#filter_invoice_expedisi').val();
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
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'NOMUAT' },
                { data: 'TGLMUAT' },
                { data: 'CUSTOMER' },
                { data: 'PESANAN' },
                { data: 'GB' },
                { data: 'PESANANGB' },
                { data: 'rute' },
                { data: 'NAMA_KENDARAAN' },
                { data: 'JUMLAH' },
                { data: 'harga_formatted' },
                { data: 'DISC' },
                { data: 'dc_formatted' },
                { data: 'total_formatted' },
                { data: 'NOSJ' },
                { data: 'INVOICE' },
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
            selectedRowData = null;
        } else {
            // hapus selection lain
            $('#InvoiceExpTable tbody tr.selected').removeClass('selected');
            // select baris ini
            $(this).addClass('selected');
            selectedRowData = tableInvoiceExp.row(this).data();
        }
    });
    // ========================== End Of Highlight No Muat Table ==================================
    // ================================ Pilih No Muat ====================================
    $('#gabungInvExpBtn').on('click', function () {
        if (!selectedRowData) {
            // alert('Pilih data terlebih dahulu');
            Swal.fire({
                title: 'Pilih data !!!',
                text: `Pilih data terlebih dahulu.`,
                icon: 'warning',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#f39c12', // Warna oranye peringatan
            });
            return;
        }
        // 🚫 SUDAH ADA INVOICE
        if (selectedRowData.INVOICE && selectedRowData.INVOICE.trim() !== '') {
            alert('Invoice sudah terbentuk untuk data ini');
            return;
        }
        let customerKode = selectedRowData.CUSTOMER_KODE;
        console.log('ngah :' + customerKode);
        // 🔥 ambil semua NOMUAT customer tsb yg invoice kosong
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            loadDataGabung(customerKode);
        });
    });
    function loadDataGabung(customerKode) {
        // destroy dulu kalau sudah ada
        if ($.fn.DataTable.isDataTable('#GabungExpTableRight')) {
            $('#GabungExpTableRight').DataTable().destroy();
        }

        $('#GabungExpTableRight').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            paging: false,
            info: false,
            ajax: {
                url: "{{ route('expedisiInvoiceGabung.data') }}",
                data: {
                    customer_kode: customerKode
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false },
                { data: 'NOMUAT' },
                { data: 'TGLMUAT' },
                { data: 'NOSJ' },
                { data: 'PESANAN' },
                { data: 'rute' },
                { data: 'NAMA_KENDARAAN' },
                { data: 'JUMLAH' },
                { data: 'harga_formatted' }
            ],
            initComplete: function () {
                // Data sudah siap
                $('#loading_modal').modal('hide');
                $('#master_form_exp_inv').addClass('d-none');
                $('#form_gabung_inv_exp').removeClass('d-none');
            }
        });
    }
    // ============================ End Of Pilih No Muat ==================================
    // ============================ Highlight Tabel Gabung ==================================
    let selectedGabungRows = [];
    $('#GabungExpTableRight tbody').on('click', 'tr', function () {
        $(this).toggleClass('selected');
        let table = $('#GabungExpTableRight').DataTable();
        let data = table.row(this).data();
        if (!data) return;
        let index = selectedGabungRows.findIndex(r => r.NOMUAT === data.NOMUAT);
        if ($(this).hasClass('selected')) {
            if (index === -1) selectedGabungRows.push(data);
        } else {
            if (index !== -1) selectedGabungRows.splice(index, 1);
        }
    });
    // ============================ End Of Highlight Tabel Gabung ==================================
    // ================================= Select Tabel Gabung ==================================
    let leftTable = $('#GabungExpTableLeft').DataTable({
        processing: true,
        serverSide: false,
        paging: false,
        searching: false,
        info: false,
        columns: [
            {
                data: null,
                orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { data: 'NOMUAT' },
            { data: 'TGLMUAT' },
            { data: 'NOSJ' },
            { data: 'PESANAN' },
            { data: 'rute' },
            { data: 'NAMA_KENDARAAN' },
            { data: 'JUMLAH' },
            { data: 'harga_formatted' },
            {
                data: null,
                orderable: false,
                width: '40px',
                render: function () {
                    return `
                        <button class="btn btn-sm btn-outline-danger btn-remove-left">
                            <i class="bx bx-trash"></i>
                        </button>
                    `;
                }
            }
        ]
    });

    $('#addToInvExpBtn').on('click', function () {
        if (selectedGabungRows.length === 0) {
            // alert('Pilih data yang akan digabung');
            Swal.fire({
                title: 'Pilih data !!!',
                text: `Pilih data yang akan digabung.`,
                icon: 'warning',
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#f39c12', // Warna oranye peringatan
            });
            return;
        }

        let leftTable  = $('#GabungExpTableLeft').DataTable();
        let rightTable = $('#GabungExpTableRight').DataTable();

        selectedGabungRows.forEach(row => {
            // 🔍 CEK APAKAH JENIS PESANAN SUDAH SAMA
            let jenisRow = getJenisPesanan(row.PESANAN);
            // ambil jenis dari data yang sudah ada di tabel kiri
            let leftData = leftTable.rows().data().toArray();
            if (leftData.length > 0) {
                let jenisExisting = getJenisPesanan(leftData[0].PESANAN);

                if (jenisExisting !== jenisRow) {
                    Swal.fire({
                        title: 'Tidak Bisa Digabung!',
                        text: `Pesanan ${jenisExisting} tidak bisa digabung dengan ${jenisRow}.`,
                        icon: 'error',
                        confirmButtonText: 'Saya Mengerti',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }
            // 🔍 CEK APAKAH NOSJ SUDAH ADA DI TABEL KIRI
            let exists = leftTable
                .rows()
                .data()
                .toArray()
                .some(r => r.NOSJ === row.NOSJ);
            if (exists) {
                // alert(`Surat Jalan ${row.NOSJ} sudah ditambahkan`);
                Swal.fire({
                    title: 'Data Sudah Ada!',
                    text: `Surat Jalan ${row.NOSJ} sudah terdaftar di sistem.`,
                    icon: 'warning',
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#f39c12', // Warna oranye peringatan
                });
                return; // skip row ini
            }
            // ➕ tambahkan ke tabel kiri
            leftTable.row.add(row).draw(false);
            // ❌ hapus dari tabel kanan
            rightTable
                .rows(function (idx, data) {
                    return data.NOSJ === row.NOSJ;
                })
                .remove();
        });
        rightTable.draw(false);
        selectedGabungRows = [];
    });
    // ============================ End Of Select Tabel Gabung ==================================
    // ================================= Delete Tabel Gabung ==================================
    $('#GabungExpTableLeft tbody').on('click', '.btn-remove-left', function () {
        let row = leftTable.row($(this).closest('tr'));
        let rowData = row.data();

        // hapus dari tabel kiri
        row.remove().draw(false);

        // 🔁 (opsional) balikin ke tabel kanan
        let rightTable = $('#GabungExpTableRight').DataTable();
        rightTable.row.add(rowData).draw(false);
    });
    // ============================ End Of Delete Tabel Gabung ==================================
});
    // ########################################################################
    // FUNCTION HELPER:
    // ########################################################################
    // Fungsi untuk membedakan eceran dan booking
    function getJenisPesanan(pesanan) {
        if (!pesanan) return null;

        if (pesanan.toUpperCase().startsWith('EC')) {
            return 'ECERAN';
        }

        if (pesanan.toUpperCase().startsWith('BOK')) {
            return 'BOOKING';
        }

        return null;
    }
</script>


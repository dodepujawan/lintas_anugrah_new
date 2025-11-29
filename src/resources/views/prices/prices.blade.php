<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1 class="h2 text-dark mb-3">FORM PRICE EXPEDISI</h1>

            <!-- Form Input -->
            <div id="formContainer" class="card border-0 bg-light mb-3" style="display: none;">
                <div class="card-body">
                    <form id="priceForm">
                        @csrf
                        <input type="hidden" id="priceId" name="id">

                        <div class="row g-4">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">RUTE</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <select id="RUTE" name="RUTE" class="form-select" style="width: 200px;" required>
                                            <option value="">Pilih Rute</option>
                                            <!-- Options akan di-load via JavaScript -->
                                        </select>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRuteModal">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">DARI - SAMPAI (Kg)</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" id="DARI" name="DARI"
                                                class="form-control" placeholder="0" required>
                                        <span class="fw-semibold">-</span>
                                        <input type="number" id="SAMPAI" name="SAMPAI"
                                                class="form-control" placeholder="0" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">JENIS</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="JENIS" value="E" id="jenisEceran" required>
                                            <label class="form-check-label" for="jenisEceran">
                                                Eceran
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="JENIS" value="B" id="jenisBoking" required>
                                            <label class="form-check-label" for="jenisBoking">
                                                Boking
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <label class="form-label fw-semibold"></label>
                                        <input type="number" id="jenis_val" name="jenis_vals" class="form-control" placeholder="" readonly>
                                    </div>
                                </div>

                                <!-- Input untuk Eceran -->

                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Item</label>
                                    <input type="text" id="KETERANGAN" name="KETERANGAN"
                                            class="form-control" placeholder="Contoh: EC > 40 KG" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">HARGA (KG)</label>
                                    <input type="number" id="HARGA" name="HARGA"
                                            class="form-control" placeholder="0" required>
                                </div>

                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" id="cancelBtn"
                                    class="btn btn-secondary">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="btn btn-primary">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tombol Tambah Data -->
            <button id="toggleFormBtn" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah Data Baru
            </button>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0 text-dark">Data Price Expedition</h2>
        </div>
        <div class="card-body">
            <table id="priceTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
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
                <tbody>
                    <!-- Data akan di-load oleh DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Rute -->
<div class="modal fade" id="addRuteModal" tabindex="-1" aria-labelledby="addRuteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRuteModalLabel">Tambah Rute Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ruteForm">
                    @csrf
                    <div class="mb-3">
                        <label for="newRute" class="form-label">Nama Rute</label>
                        <input type="text" class="form-control" id="newRute" name="RUTE" placeholder="Contoh: DIY - DPS" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveRute()">Simpan</button>
            </div>
        </div>
    </div>
</div>
 <script>
$(document).ready(function() {
    // Load data rute untuk select
    loadRuteOptions();

    // Initialize DataTable
    var table = $('#priceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('price-expedition.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'KETERANGAN', name: 'KETERANGAN' },
            { data: 'DARI', name: 'DARI' },
            { data: 'SAMPAI', name: 'SAMPAI' },
            { data: 'RUTE', name: 'RUTE' },
            { data: 'HARGA', name: 'HARGA' },
            { data: 'JENIS', name: 'JENIS' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            search: "Cari:",
            lengthMenu: "Tampil _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Toggle form show/hide
    $('#toggleFormBtn').click(function() {
        $('#formContainer').toggle();
        resetForm();
    });

    $('#cancelBtn').click(function() {
        $('#formContainer').hide();
        resetForm();
    });

    // Toggle fields berdasarkan jenis
    $('input[name="JENIS"]').change(function() {
        const jenis = $(this).val();
        toggleJenisFields(jenis);
    });

    // Reset form
    function resetForm() {
        $('#priceForm')[0].reset();
        $('#priceId').val('');
        $('#submitBtn').text('Simpan');
        $('#bokingFields').hide();
        // Clear validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    }

    // Toggle fields berdasarkan jenis
    function toggleJenisFields(jenis) {
        if (jenis === 'E') {
            $('#jenis_val').val(1);
        } else if (jenis === 'B') {
            $('#jenis_val').val(2);
        }
    }

    // Form submission
    $('#priceForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var url = $('#priceId').val() ? '/price-expedition/' + $('#priceId').val() : '/price-expedition';
        var method = $('#priceId').val() ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#formContainer').hide();
                    resetForm();
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();

                    // Display new errors
                    $.each(errors, function(field, messages) {
                        var input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                    });
                }
            }
        });
    });
});

// Load rute options
function loadRuteOptions() {
    $.ajax({
        url: "{{ route('rute.data') }}", // Route untuk mengambil data rute
        method: 'GET',
        success: function(response) {
            const select = $('#RUTE');
            select.empty();
            select.append('<option value="">Pilih Rute</option>');

            response.data.forEach(function(rute) {
                select.append(`<option value="${rute.RUTE}">${rute.RUTE}</option>`);
            });
        }
    });
}

// Save new rute
function saveRute() {
    const formData = new FormData(document.getElementById('ruteForm'));

    $.ajax({
        url: '/rute',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                // Tutup modal
                $('#addRuteModal').modal('hide');
                // Reset form
                $('#ruteForm')[0].reset();
                // Langsung tambahkan option baru tanpa reload semua
                $('#RUTE').append(new Option(response.data.RUTE, response.data.RUTE));
                // Set sebagai selected
                $('#RUTE').val(response.data.RUTE);
                alert('Rute berhasil ditambahkan!' + response.data.RUTE);
            }
        },
        error: function(xhr) {
            var errors = xhr.responseJSON.errors;
            if (errors) {
                alert(errors[Object.keys(errors)[0]][0]);
            }
        }
    });
}

// Edit data
function editData(id) {
    $.ajax({
        url: '/price-expedition/' + id,
        method: 'GET',
        success: function(response) {
            $('#priceId').val(response.ID);
            $('#KETERANGAN').val(response.KETERANGAN);
            $('#DARI').val(response.DARI);
            $('#SAMPAI').val(response.SAMPAI);

            // Extract tujuan dari RUTE (remove "DPS-")
            var rute = response.RUTE;
            if (rute.startsWith('DPS-')) {
                rute = rute.substring(4);
            }
            $('#RUTE').val(rute);

            $('#HARGA').val(response.HARGA);
            $('#HV').val(response.HV);
            $('#HKG').val(response.HKG);
            $('#HBOK').val(response.HBOK);

            // Set radio button dan toggle fields
            $('input[name="JENIS"][value="' + response.JENIS + '"]').prop('checked', true);
            toggleJenisFields(response.JENIS);

            $('#submitBtn').text('Update');
            $('#formContainer').show();
        }
    });
}

// Delete data
function deleteData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        $.ajax({
            url: '/price-expedition/' + id,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#priceTable').DataTable().ajax.reload();
                }
            }
        });
    }
}
</script>

<div class="container mt-4">
    <h2>Data Kendaraan</h2>
    <!-- Tabel Data -->
    <div class="card-kendaraan" id="tableKendaraan">
        <div>
            <button class="btn btn-primary" id="add_kendaraan">+Add Kendaraan</button>
        </div>
        <div class="card-kendaraan-header">
            <h5>Daftar Kendaraan</h5>
        </div>
        <div class="card-kendaraan-body">
            <table class="table table-bordered" id="kendaraanTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Plat</th>
                        <th>Jenis</th>
                        <th>FNO PRK B</th>
                        <th>FNO PRK P</th>
                        <th>FNO PRK S</th>
                        <th>FNO PRK O</th>
                        <th>FNO PRK M</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- Form Input -->
    <div class="card-kendaraan mb-4" style="display: none;" id="formKendaraan">
        <div class="card-kendaraan-header">
            <h5>Form Tambah Kendaraan</h5>
        </div>
        <div>
            <button class="btn btn-primary" id="list_kendaraan">List Kendaraan</button>
        </div>
        <div class="card-kendaraan-body">
            <form id="kendaraanForm">
                @csrf
                <input type="hidden" id="id_flag" name="id_flag">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input type="text" class="form-control" id="kode" name="kode" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="plat" class="form-label">Plat</label>
                            <input type="text" class="form-control" id="plat" name="plat" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="jens" class="form-label">Jenis</label>
                            <input type="text" class="form-control" id="jens" name="jens" required>
                        </div>
                        <div class="mb-3">
                            <label for="fno_prk_b" class="form-label">FNO PRK B</label>
                            <input type="text" class="form-control" id="fno_prk_b" name="fno_prk_b" required>
                        </div>
                        <div class="mb-3">
                            <label for="fno_prk_p" class="form-label">FNO PRK P</label>
                            <input type="text" class="form-control" id="fno_prk_p" name="fno_prk_p" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fno_prk_s" class="form-label">FNO PRK S</label>
                            <input type="text" class="form-control" id="fno_prk_s" name="fno_prk_s" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fno_prk_o" class="form-label">FNO PRK O</label>
                            <input type="text" class="form-control" id="fno_prk_o" name="fno_prk_o" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="fno_prk_m" class="form-label">FNO PRK M</label>
                            <input type="text" class="form-control" id="fno_prk_m" name="fno_prk_m" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()">Batal</button>
            </form>
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
    // ============================ Initialize DataTable ============================
    var table = $('#kendaraanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('kendaraan.data') }}",
        // Scroll settings
        scrollX: true,
        scrollY: "400px",
        scrollCollapse: true,
        // Responsive settings
        responsive: true,
        autoWidth: false,
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'kode', name: 'kode'},
            {data: 'nama', name: 'nama'},
            {data: 'plat', name: 'plat'},
            {data: 'jens', name: 'jens'},
            {data: 'fno_prk_b', name: 'fno_prk_b'},
            {data: 'fno_prk_p', name: 'fno_prk_p'},
            {data: 'fno_prk_s', name: 'fno_prk_s'},
            {data: 'fno_prk_o', name: 'fno_prk_o'},
            {data: 'fno_prk_m', name: 'fno_prk_m'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    // ======================== End Of Initialize DataTable ==========================
    // ================================== Form SHow / Hide ===============================
    $("#add_kendaraan").click( function(e) {
        e.preventDefault();
        $("#tableKendaraan").hide();
        $("#formKendaraan").show();
        resetForm();
    });
    $("#list_kendaraan").click( function(e) {
        e.preventDefault();
        $("#tableKendaraan").show();
        $("#formKendaraan").hide();
        // Refresh DataTable
        table.ajax.reload();
    });
    // ============================== End Of Form SHow / Hide ============================
    // ==================== Handle Enter key untuk semua input di form ===================
    $('#kendaraanForm input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();

            var $this = $(this);
            var index = $('#kendaraanForm input').index(this);
            var nextIndex = index + 1;

            // Jika bukan input terakhir, focus ke next
            if (nextIndex < $('#kendaraanForm input').length) {
                $('#kendaraanForm input').eq(nextIndex).focus();
            } else {
                // Jika input terakhir, submit form
                $('#kendaraanForm').submit();
            }
        }
    });
    // ================== ENd Of Handle Enter key untuk semua input di form ===================
    // ============================== Submit Form ============================
    $('#kendaraanForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var url = $('#id_flag').val() ? "{{ route('kendaraan.update', ':id') }}".replace(':id', $('#id_flag').val()): "{{ route('kendaraan.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire('Sukses!', response.success, 'success');
                resetForm();
                $("#tableKendaraan").show();
                $("#formKendaraan").hide();
                table.ajax.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value + '<br>';
                });
                Swal.fire('Error!', errorMessage, 'error');
            }
        });
    });
    // ============================ ENd Of Submit Form ===============================
    // Edit Data
    $(document).on('click', '.edit', function() {
        var id = $(this).data('id');
        $("#tableKendaraan").hide();
        $("#formKendaraan").show();

        $.get("{{ route('kendaraan.edit', ':id') }}".replace(':id', id), function(data) {
            console.log("aba" + data.id + data.kode + data.nama);
            $('#id_flag').val(data.id);
            $('#kode').val(data.kode);
            $('#nama').val(data.nama);
            $('#plat').val(data.plat);
            $('#jens').val(data.jens);
            $('#fno_prk_b').val(data.fno_prk_b);
            $('#fno_prk_p').val(data.fno_prk_p);
            $('#fno_prk_s').val(data.fno_prk_s);
            $('#fno_prk_o').val(data.fno_prk_o);
            $('#fno_prk_m').val(data.fno_prk_m);

            $('html, body').animate({
                scrollTop: $(".card-kendaraan-header").offset().top
            }, 500);
        });
    });

    // Delete Data
    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('kendaraan') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire('Terhapus!', response.success, 'success');
                        table.ajax.reload();
                    }
                });
            }
        });
    });
});

function resetForm() {
    $('#kendaraanForm')[0].reset();
    $('#id').val('');
}
</script>

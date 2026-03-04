<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Form Supplier / Leasing</h5>
    </div>

    <div class="card-body">
        <form id="form_supplier">

            {{-- ========================= --}}
            {{-- INFORMASI UMUM --}}
            {{-- ========================= --}}
            <h6 class="border-bottom pb-2 mb-3 text-primary">
                Informasi Umum
            </h6>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori_supplier" id="kategori_supplier"
                        class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="SP">Supplier</option>
                        <option value="LS">Leasing</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kode (Auto)</label>
                    <input type="text" id="kode_supplier"
                        class="form-control bg-light" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama_supplier"
                        id="nama_supplier"
                        class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Alamat 1</label>
                    <input type="text" name="alamat1_supplier"
                        id="alamat1_supplier"
                        class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Alamat 2</label>
                    <input type="text" name="alamat2_supplier"
                        id="alamat2_supplier"
                        class="form-control">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota_supplier"
                        id="kota_supplier"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="telepon_supplier"
                        id="telepon_supplier"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fax</label>
                    <input type="text" name="fax_supplier"
                        id="fax_supplier"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email_supplier"
                        id="email_supplier"
                        class="form-control">
                </div>
            </div>

            {{-- ========================= --}}
            {{-- INFORMASI KONTAK --}}
            {{-- ========================= --}}
            <h6 class="border-bottom pb-2 mb-3 text-primary">
                Informasi Kontak
            </h6>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" name="kontak_supplier"
                        id="kontak_supplier"
                        class="form-control">
                </div>
            </div>

            {{-- ========================= --}}
            {{-- INFORMASI BANK --}}
            {{-- ========================= --}}
            <h6 class="border-bottom pb-2 mb-3 text-primary">
                Informasi Bank
            </h6>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Nama Bank</label>
                    <input type="text" name="bank_supplier"
                        id="bank_supplier"
                        class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nomor Rekening</label>
                    <input type="text" name="norek_supplier"
                        id="norek_supplier"
                        class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Atas Nama</label>
                    <input type="text" name="atasnama_supplier"
                        id="atasnama_supplier"
                        class="form-control">
                </div>
            </div>

            {{-- ========================= --}}
            {{-- FIELD KEUANGAN (HIDDEN) --}}
            {{-- ========================= --}}
            <input type="hidden" name="saldo_supplier" value="0">
            <input type="hidden" name="returan_supplier" value="0">
            <input type="hidden" name="ftop_supplier" value="0">
            <input type="hidden" name="disc_reg_supplier" value="0">

            <div class="text-end">
                <button type="reset" class="btn btn-secondary me-2">
                    Reset
                </button>

                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>

        </form>
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
// ================================= Store Supplier =====================================
    $('#form_supplier').on('submit', function(e){
        e.preventDefault();
        $('#loading_modal').modal('show');
        $('#loading_modal').one('shown.bs.modal', function () {
            $.ajax({
                url: "{{ route('msupplier.store') }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res){

                    if(res.status){
                        $('#loading_modal').modal('hide');
                        $('#kode_supplier').val(res.kode);

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Supplier berhasil disimpan, nomer: ' + res.kode,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $('#form_supplier')[0].reset();

                    } else {
                        $('#loading_modal').modal('hide');
                        alert(res.message);
                    }

                },
                error: function(){
                    $('#loading_modal').modal('hide');
                    alert('Terjadi kesalahan server');
                }
            });
        });

    });
// ============================== End Of Store Supplier ==================================
});
</script>

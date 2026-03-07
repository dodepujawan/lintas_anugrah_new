<style>
.form-label {
    font-weight: 600;
}
.card-header {
    font-size: 18px;
    letter-spacing: 1px;
}
</style>
<div class="container mt-4">
```
<div class="card shadow">
    <div class="card-header bg-success text-white fw-bold">
        SERVICE MOBIL
    </div>
    <div class="card-body">
        <form id="formService">
            @csrf

            <div class="row">

                <!-- KIRI -->
                <div class="col-md-7 mt-3">

                    <div class="row mb-2">
                        <label class="col-sm-4 col-form-label">AKUN HUTANG</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="akun_hutang" id="akun_hutang" value="21000">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="akun_hutang_nama" id="akun_hutang_nama" value="HUTANG USAHA">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-4 col-form-label">SUPPLIER</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="supplier" id="supplier">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-warning w-100">🔍</button>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="supplier_nama" id="supplier_nama">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-4 col-form-label">KENDARAAN</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="kendaraan_supplier" id="kendaraan_supplier">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-warning w-100">🔍</button>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="kendaraan_supplier_nama" id="kendaraan_supplier_nama">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-4 col-form-label">AKUN BIAYA MOBIL</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" name="fno_prk_b_supplier" id="fno_prk_b_supplier" value="56502">
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" name="fno_prk_b_supplier_nama" id="fno_prk_b_supplier_nama">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-4 col-form-label">NILAI SERVICE</label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control text-end" name="nilai_servis" id="nilai_servis" value="0">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-4 col-form-label">ITEM BARANG</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" rows="3" name="keterangan"></textarea>
                        </div>
                    </div>

                </div>

                <!-- KANAN -->
                <div class="col-md-5 mt-3">

                    <div class="row mb-2">
                        <label class="col-sm-5 col-form-label">NOMOR FAKTUR</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="no_faktur">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-5 col-form-label">TGL.DOCUMENT</label>
                        <div class="col-sm-7">
                            <input type="date" class="form-control" name="tgl_document">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-5 col-form-label">TGL.JTH.TEMPO</label>
                        <div class="col-sm-7">
                            <input type="date" class="form-control" name="tgl_jatuh_tempo">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <label class="col-sm-5 col-form-label">NO.JURNAL</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="no_jurnal">
                        </div>
                    </div>

                </div>

            </div>

            <hr>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success">💾 SIMPAN</button>
                <button class="btn btn-secondary">🆕 BARU</button>
                <button class="btn btn-danger">❌ HAPUS</button>
            </div>

        </form>

    </div>

</div>
```
</div>
<script>
$(document).ready(function(){
    // Set CSRF token in AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
// ================================= Submit Service =====================================
    $('#formService').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('servis.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response){

                if(response.status){

                    alert(response.message);

                    $('#formService')[0].reset();

                }else{

                    alert(response.message);

                }

            },
            error: function(xhr){

                alert('Terjadi kesalahan server');

            }
        });

    });
// =============================== End Of Submit Service ==================================
});
</script>


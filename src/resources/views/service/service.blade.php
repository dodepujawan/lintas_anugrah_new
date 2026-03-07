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

    <div class="card-body" id="tableServiceMaster">
        <div class="mt-3">
            <button class="btn btn-primary" id="add_service">
                + Tambah
            </button>
        </div>
        <table class="table table-bordered" id="tableService">
            <thead>
                <tr>
                    <th>No Bukti</th>
                    <th>Supplier</th>
                    <th>Kendaraan</th>
                    <th>Nilai Service</th>
                    <th>Tanggal</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="card-body d-none" id="formServiceMaster">
        <div class="mt-3">
            <button class="btn btn-success" id="return_table_service">
            << Kembali
            </button>
        </div>
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
                <button type="submit" class="btn btn-success" id="button_service_submit">💾 SIMPAN</button>
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
// ================================= Table Service =====================================
    if ($.fn.DataTable.isDataTable('#tableService')) {
        $('#tableService').DataTable().destroy();
    }
    $('#tableService').DataTable({

        processing: true,
        serverSide: true,

        ajax: "{{ route('service.data') }}",

        columns: [

            { data:'NO_SERVICE', name:'NO_SERVICE' },
            { data:'KODE_SUPPLIER', name:'KODE_SUPPLIER' },
            { data:'KODE_MOBIL', name:'KODE_MOBIL' },
            { data:'NILAI_SERVIS', name:'NILAI_SERVIS' },
            { data:'TGL_SERVIS', name:'TGL_SERVIS' },
            { data:'action', name:'action', orderable:false, searchable:false }

        ]

    });
// ============================== End Of Table Service ==================================
// ================================= Add Supplier ====================================
    $('#add_service').on('click', function(e){
        $('#tableServiceMaster').addClass('d-none');
        $('#formServiceMaster').removeClass('d-none');
        $('#formService')[0].reset();
        $('#button_service_submit')
        .removeClass('btn btn-success')
        .addClass('btn btn-primary')
        .html('<i class="bx bx-save"></i> SIMPAN');
    });
// ============================== End Of Add Supplier ==================================
// ================================= Return Table Supplier ====================================
    $('#return_table_service').on('click', function(e){
        $('#formServiceMaster').addClass('d-none');
        $('#tableServiceMaster').removeClass('d-none');
        $('#tableService').DataTable().ajax.reload();
    });
// ============================== End Of Return Table Supplier =================================
// ================================= Submit Service =====================================
    $('#formService').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('service.store') }}",
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


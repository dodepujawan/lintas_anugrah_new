<div class="container-fluid py-2">
    {{-- ===================================================== --}}
    {{-- HEADER --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm border-0 mb-2">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">INVOICE EXPEDISI</h4>
                <small class="text-muted">Data invoice expedisi EKS</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-dark btn-sm" id="btn_filter_invoice_eks">
                    <i class="fa fa-search"></i> Reload
                </button>
            </div>
        </div>
    </div>
    {{-- ===================================================== --}}
    {{-- FILTER --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm border-0 mb-2">
        <div class="card-body py-2">
            <div class="row g-2">
                <div class="col-lg-2">
                    <label class="form-label mb-1">DARI</label>
                    <input type="date" class="form-control form-control-sm" id="tanggal_dari_invoice_eks">
                </div>
                <div class="col-lg-2">
                    <label class="form-label mb-1">SAMPAI</label>
                    <input type="date" class="form-control form-control-sm" id="tanggal_sampai_invoice_eks">
                </div>
                <div class="col-lg-3">
                    <label class="form-label mb-1">CARI</label>
                    <input type="text" class="form-control form-control-sm" id="search_invoice_eks" placeholder="Invoice / Customer">
                </div>
            </div>
        </div>
    </div>
    {{-- ===================================================== --}}
    {{-- TABLE --}}
    {{-- ===================================================== --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm align-middle w-100" id="table_invoice_eks">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">NO</th>
                            <th width="110">TGL</th>
                            <th width="160">INVOICE</th>
                            <th>CUSTOMER</th>
                            <th width="160" class="text-end">GRAND</th>
                            <th width="160" class="text-end">PIUTANG</th>
                            <th width="160" class="text-end">BAYAR</th>
                            <th width="120">AKSI</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- ===================================================== --}}
{{-- MODAL EDIT --}}
{{-- ===================================================== --}}
<div class="modal fade" id="modal_invoice_edit" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            {{-- ================================================= --}}
            {{-- HEADER --}}
            {{-- ================================================= --}}
            <div class="modal-header bg-success text-white py-2">
                <div>
                    <h5 class="modal-title fw-bold mb-0">EDIT INVOICE EXPEDISI</h5>
                    <small>Desktop Layout - Minim Scroll</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            {{-- ================================================= --}}
            {{-- BODY --}}
            {{-- ================================================= --}}
            <div class="modal-body bg-light p-2">
                <div class="row g-2">
                    {{-- ============================================= --}}
                    {{-- LEFT --}}
                    {{-- ============================================= --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2">
                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">INVOICE</label>
                                    <input type="text" class="form-control form-control-sm bg-secondary-subtle" id="invoice_edit" readonly>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">CUSTOMER</label>
                                    <input type="text" class="form-control form-control-sm" id="customer_invoice_edit">
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">TGL INV</label>
                                        <input type="date" class="form-control form-control-sm" id="tgl_invoice_edit">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">TGL JT</label>
                                        <input type="date" class="form-control form-control-sm" id="tgl_jt_invoice_edit">
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">BAYAR</label>
                                        <input type="text" class="form-control form-control-sm text-end fw-bold bg-success-subtle" id="bayar_invoice_edit" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold mb-1">PIUTANG</label>
                                        <input type="text" class="form-control form-control-sm text-end fw-bold bg-warning-subtle" id="piutang_invoice_edit">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-1">KETERANGAN</label>
                                    <textarea class="form-control form-control-sm" rows="4" id="keterangan_invoice_edit"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ============================================= --}}
                    {{-- RIGHT --}}
                    {{-- ============================================= --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-2">
                                {{-- ===================================== --}}
                                {{-- TOTAL --}}
                                {{-- ===================================== --}}
                                <div class="row g-2 mb-2">
                                    <div class="col-lg-3">
                                        <label class="form-label fw-bold mb-1">SUBTOTAL</label>
                                        <input type="text" class="form-control form-control-sm text-end" id="subtotal_invoice_edit">
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label fw-bold mb-1">DISKON</label>
                                        <input type="text" class="form-control form-control-sm text-end" id="diskon_invoice_edit">
                                    </div>
                                    <div class="col-lg-2">
                                        <label class="form-label fw-bold mb-1">PPN</label>
                                        <input type="text" class="form-control form-control-sm text-end" id="ppn_invoice_edit">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-label fw-bold mb-1">GRAND</label>
                                        <input type="text" class="form-control form-control-sm text-end fw-bold bg-danger-subtle" id="grand_invoice_edit">
                                    </div>
                                    <div class="col-lg-2 d-flex align-items-end">
                                        <button class="btn btn-success btn-sm w-100" id="btn_simpan_invoice_edit">
                                            <i class="fa fa-save"></i> SIMPAN
                                        </button>
                                    </div>
                                </div>
                                {{-- ===================================== --}}
                                {{-- TABLE DETAIL --}}
                                {{-- ===================================== --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm align-middle w-100" id="table_detail_invoice_edit">
                                        <thead class="table-success">
                                            <tr>
                                                <th width="120">NO.SJ</th>
                                                <th width="120" class="text-end">JUMLAH</th>
                                                <th width="150" class="text-end">HARGA AW</th>
                                                <th width="150" class="text-end">TOTAL</th>
                                            </tr>
                                        </thead>
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
<script>
$(document).ready(function() {
    // Set CSRF token in AJAX setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
// ========================================= Show Table ================================================
    // =====================================================
    // TABLE INVOICE
    // =====================================================
    let table_invoice_eks = $('#table_invoice_eks').DataTable({

        processing:true,
        serverSide:true,
        scrollX:true,
        pageLength:25,

        ajax:{
            url:'{{ route("expedisiInvoiceEdit.data") }}',
            data:function(d){
                d.tanggal_dari = $('#tanggal_dari_invoice_eks').val();
                d.tanggal_sampai = $('#tanggal_sampai_invoice_eks').val();
                d.search = $('#search_invoice_eks').val();
            }
        },
        columns:[
                    {
                        data:'DT_RowIndex',
                        name:'DT_RowIndex',
                        orderable:false,
                        searchable:false
                    },
                    {
                        data:'TGLINVOICE'
                    },
                    {
                        data:'INVOICE'
                    },
                    {
                        data:'CUSTOMER'
                    },
                    {
                        data:'GRAND',
                        className:'text-end'
                    },
                    {
                        data:'PIUTANG',
                        className:'text-end'
                    },
                    {
                        data:'bayar',
                        className:'text-end fw-bold text-success'
                    },
                    {
                        data:'aksi',
                        orderable:false,
                        searchable:false,
                        className:'text-center'
                    }
                ]
    });
    // =====================================================
    // FILTER
    // =====================================================
    $('#btn_filter_invoice_eks').click(function(){
        table_invoice_eks.ajax.reload();
    });
    // =====================================================
    // TABLE DETAIL
    // =====================================================
    let table_detail_invoice_edit = $('#table_detail_invoice_edit').DataTable({
        paging:false,
        searching:false,
        info:false,
        ordering:false,
        scrollY:'350px',
        scrollCollapse:true,
        columns:[
            {data:'nosj'},
            {data:'jumlah',className:'text-end'},
            {data:'harga',className:'text-end'},
            {data:'total',className:'text-end fw-bold'}
        ]
    });
// ==================================== End Of Show Table ===============================================
// ======================================== Show Invoice =================================================
    // $(document).on('click','.btn_edit_invoice_eks',function(){
    //     let invoice=$(this).data('invoice');
    //     $.get('/invoice-eks/'+invoice+'/edit',function(r){
    //         // ==========================================
    //         // MASTER
    //         // ==========================================
    //         $('#invoice_edit').val(r.master.invoice);
    //         $('#customer_invoice_edit').val(r.master.customer);

    //         $('#tgl_invoice_edit').val(r.master.tgl_invoice);
    //         $('#tgl_jt_invoice_edit').val(r.master.tgl_jt);

    //         $('#subtotal_invoice_edit').val(r.master.subtotal);
    //         $('#diskon_invoice_edit').val(r.master.diskon);
    //         $('#ppn_invoice_edit').val(r.master.ppn);
    //         $('#grand_invoice_edit').val(r.master.grand);

    //         $('#bayar_invoice_edit').val(r.master.bayar);
    //         $('#piutang_invoice_edit').val(r.master.piutang);

    //         $('#keterangan_invoice_edit').val(r.master.keterangan);

    //         // ==========================================
    //         // DETAIL
    //         // ==========================================
    //         table_detail_invoice_edit.clear();
    //         r.details.forEach(function(d){
    //             table_detail_invoice_edit.row.add({
    //                 nosj:d.nosj,
    //                 jumlah:d.jumlah,
    //                 harga:d.harga,
    //                 total:d.total
    //             });
    //         });
    //         table_detail_invoice_edit.draw();
    //         $('#modal_invoice_edit').modal('show');
    //     });
    // });
// ==================================== End Of Show Invoice ===============================================
// ======================================= Submit Invoice ================================================
    // $('#btn_simpan_invoice_edit').click(function(){

    //     let formData={

    //         invoice:$('#invoice_edit').val(),
    //         customer:$('#customer_invoice_edit').val(),

    //         tgl_invoice:$('#tgl_invoice_edit').val(),
    //         tgl_jt:$('#tgl_jt_invoice_edit').val(),

    //         subtotal:$('#subtotal_invoice_edit').val(),
    //         diskon:$('#diskon_invoice_edit').val(),
    //         ppn:$('#ppn_invoice_edit').val(),
    //         grand:$('#grand_invoice_edit').val(),

    //         bayar:$('#bayar_invoice_edit').val(),
    //         piutang:$('#piutang_invoice_edit').val(),

    //         keterangan:$('#keterangan_invoice_edit').val()

    //     };

        // $.ajax({

        //     url:'/invoice-eks/update',
        //     type:'POST',
        //     data:formData,

        //     beforeSend:function(){

        //         $('#btn_simpan_invoice_edit')
        //             .prop('disabled',true)
        //             .html('<i class="fa fa-spinner fa-spin"></i> SIMPAN...');

        //     },

    //         success:function(r){

    //             $('#btn_simpan_invoice_edit')
    //                 .prop('disabled',false)
    //                 .html('<i class="fa fa-save"></i> SIMPAN');

    //             $('#modal_invoice_edit').modal('hide');

    //             table_invoice_eks.ajax.reload(null,false);

    //             Swal.fire({

    //                 icon:'success',
    //                 title:'Berhasil',
    //                 text:r.message

    //             });

    //         },

    //         error:function(xhr){

    //             $('#btn_simpan_invoice_edit')
    //                 .prop('disabled',false)
    //                 .html('<i class="fa fa-save"></i> SIMPAN');

    //             Swal.fire({

    //                 icon:'error',
    //                 title:'Oops...',
    //                 text:xhr.responseJSON.message

    //             });

    //         }

    //     });

    // });
// ===================================== End Of Submit Invoice =============================================
});
</script>

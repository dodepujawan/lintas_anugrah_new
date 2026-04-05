<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            <h5>Hak Akses User</h5>
        </div>

        <div class="mb-3">
            <label>Pilih User</label>
            <select id="userSelect" class="form-select">
                <option value="">-- Pilih User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- ================= CUSTOMER ================= --}}
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold">Customer</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="customer.view">
                        <label class="form-check-label">Customer</label>
                    </div>
                </div>

                {{-- ================= KENDARAAN ================= --}}
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold">Kendaraan</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="kendaraan.view">
                        <label class="form-check-label">Kendaraan</label>
                    </div>
                </div>

                {{-- ================= DRIVER ================= --}}
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold">Driver</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="driver.view">
                        <label class="form-check-label">Driver</label>
                    </div>
                </div>

                {{-- ================= PRICE ================= --}}
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold">Price</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="price.expedisi">
                        <label class="form-check-label">Ekspedisi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="price.customer">
                        <label class="form-check-label">Ekspedisi Customer</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="price.rent">
                        <label class="form-check-label">Rent</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="price.customer_rent">
                        <label class="form-check-label">Rent Customer</label>
                    </div>
                </div>

                {{-- ================= PENJUALAN ================= --}}
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold">Penjualan</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="penjualan.expedisi">
                        <label class="form-check-label">Ekspedisi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="penjualan.invoice">
                        <label class="form-check-label">Invoice Ekspedisi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="penjualan.kwitansi">
                        <label class="form-check-label">Kwitansi Ekspedisi</label>
                    </div>
                    <hr>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="penjualan.rent_dingin">
                        <label class="form-check-label">Sewa Mobil Pendingin</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="penjualan.invoice_rent_dingin">
                        <label class="form-check-label">Invoice Rent Dingin</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="penjualan.kwitansi_rent_dingin">
                        <label class="form-check-label">Kwitansi Rent Dingin</label>
                    </div>
                </div>

                {{-- ================= SUPPLIER ================= --}}
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold">Supplier</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="supplier.view">
                        <label class="form-check-label">Supplier</label>
                    </div>
                </div>

                {{-- ================= SERVICE ================= --}}
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold">Service</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="service.view">
                        <label class="form-check-label">Service</label>
                    </div>
                </div>

                {{-- ================= USER MANAGEMENT ================= --}}
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold">User Management</h6>

                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="user.view">
                        <label class="form-check-label">View User</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="user.create">
                        <label class="form-check-label">Add User</label>
                    </div>
                </div>

                {{-- ================= EXTRA ================= --}}
                <div class="col-md-6 mb-3">
                    <h6 class="fw-bold">Extra</h6>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="extra.pajak">
                        <label class="form-check-label">Pajak</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="extra.rekening">
                        <label class="form-check-label">Rekening</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="extra.signature">
                        <label class="form-check-label">Penanggung Jawab</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="extra.printer">
                        <label class="form-check-label">Printer</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input perm" type="checkbox" value="extra.permissions">
                        <label class="form-check-label">Hak Akses/Permissions</label>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary mt-3" id="saveBtn">
                Simpan Hak Akses
            </button>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {

    // ================= LOAD PERMISSION =================
    $('#userSelect').change(function () {
        let userId = $(this).val();
        // reset semua checkbox
        $('.perm').prop('checked', false);

        if (!userId) return;

        $.ajax({
            url: "{{ route('user.permissions', ':id') }}".replace(':id', userId),
            type: "GET",
            success: function (res) {
                // checklist sesuai permission user
                res.permissions.forEach(function (perm) {
                    $('.perm[value="' + perm + '"]').prop('checked', true);
                });
            },
            error: function () {
                alert('Gagal load permission');
            }
        });
    });

    // ================= SAVE PERMISSION =================
    $('#saveBtn').click(function () {
        let userId = $('#userSelect').val();

        if (!userId) {
            alert('Pilih user dulu');
            return;
        }
        let permissions = [];

        $('.perm:checked').each(function () {
            permissions.push($(this).val());
        });
        $.ajax({
            url: "{{ route('update.permissions') }}",
            type: "POST",
            data: {
                user_id: userId,
                permissions: permissions,
                _token: "{{ csrf_token() }}"
            },
            success: function (res) {

                alert('Berhasil disimpan ✅');

            },
            error: function () {
                alert('Gagal simpan ❌');
            }
        });
    });
    // ================= END OF SAVE PERMISSION =================
});
</script>

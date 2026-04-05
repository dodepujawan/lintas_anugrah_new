<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        // 🔥 RESET CACHE WAJIB
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ================= PERMISSIONS =================
        $permissions = [
            'customer.view',
            'kendaraan.view',
            'driver.view',

            'price.expedisi',
            'price.customer',
            'price.rent',
            'price.customer_rent',

            'penjualan.expedisi',
            'penjualan.invoice',
            'penjualan.kwitansi',
            'penjualan.rent_dingin',
            'penjualan.invoice_rent_dingin',
            'penjualan.kwitansi_rent_dingin',

            'supplier.view',
            'service.view',

            'user.view',
            'user.create',

            'extra.pajak',
            'extra.rekening',
            'extra.signature',
            'extra.printer',
            // 🔥 TAMBAHAN BARU
            'extra.permissions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        // ================= ROLES =================
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        // ================= ASSIGN PERMISSION KE ROLE =================
        // ### Fungsinya agar role bisa akses permission tertentu
        // $admin->syncPermissions($permissions);

        // $staff->syncPermissions([
        //     'customer.view',
        //     'kendaraan.view',
        //     'driver.view',
        //     'penjualan.expedisi',
        // ]);

        // ================= SUPER ADMIN (USER ID 1) =================
        $user = User::find(1);

        if ($user) {
            $user->assignRole('admin');
        }
    }
}

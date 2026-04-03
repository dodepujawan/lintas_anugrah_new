<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // buat permission
        Permission::firstOrCreate(['name' => 'transaksi.view']);
        Permission::firstOrCreate(['name' => 'transaksi.create']);

        // ambil role (ini versi kalau mau pakai roles sebagai parameters)
        $admin = Role::findByName('admin');
        $staff = Role::findByName('staff');

        // kasih permission ke role
        $admin->givePermissionTo(['transaksi.view', 'transaksi.create']);
        $staff->givePermissionTo(['transaksi.view']);
    }
}

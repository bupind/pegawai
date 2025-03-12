<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superuser = Role::create([
            'name'       => 'superuser',
            'guard_name' => 'web',
        ]);
        $superuser->givePermissionTo([
            'user create',
            'user read',
            'user update',
            'user delete',

            'role create',
            'role read',
            'role update',
            'role delete',
            'permission create',
            'permission read',
            'permission update',
            'permission delete',
            'setting read',


            'employee create',
            'employee read',
            'employee update',
            'employee delete',

            'license create',
            'license read',
            'license update',
            'license delete',

            'registrationcertificate create',
            'registrationcertificate read',
            'registrationcertificate update',
            'registrationcertificate delete',
        ]);
        $admin = Role::create([
            'name'       => 'pegawai',
            'guard_name' => 'web',
        ]);
        $admin->givePermissionTo([
            'user delete',
            'user update',
            'user read',
            'user create',
            'role read',
            'permission read',
        ]);
    }
}

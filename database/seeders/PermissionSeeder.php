<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'user create', 'guard_name' => 'web']);
        Permission::create(['name' => 'user read', 'guard_name' => 'web']);
        Permission::create(['name' => 'user update', 'guard_name' => 'web']);
        Permission::create(['name' => 'user delete', 'guard_name' => 'web']);

        Permission::create(['name' => 'role create', 'guard_name' => 'web']);
        Permission::create(['name' => 'role read', 'guard_name' => 'web']);
        Permission::create(['name' => 'role update', 'guard_name' => 'web']);
        Permission::create(['name' => 'role delete', 'guard_name' => 'web']);

        Permission::create(['name' => 'permission create', 'guard_name' => 'web']);
        Permission::create(['name' => 'permission read', 'guard_name' => 'web']);
        Permission::create(['name' => 'permission update', 'guard_name' => 'web']);
        Permission::create(['name' => 'permission delete', 'guard_name' => 'web']);

        Permission::create(['name' => 'setting read', 'guard_name' => 'web']);

        Permission::create(['name' => 'employee create', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee read', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee update', 'guard_name' => 'web']);
        Permission::create(['name' => 'employee delete', 'guard_name' => 'web']);

        Permission::create(['name' => 'license create', 'guard_name' => 'web']);
        Permission::create(['name' => 'license read', 'guard_name' => 'web']);
        Permission::create(['name' => 'license update', 'guard_name' => 'web']);
        Permission::create(['name' => 'license delete', 'guard_name' => 'web']);

        Permission::create(['name' => 'registrationcertificate create', 'guard_name' => 'web']);
        Permission::create(['name' => 'registrationcertificate read', 'guard_name' => 'web']);
        Permission::create(['name' => 'registrationcertificate update', 'guard_name' => 'web']);
        Permission::create(['name' => 'registrationcertificate delete', 'guard_name' => 'web']);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::create([
            'first_name'        => 'Superuser',
            'email'             => 'superuser@company.com',
            'phone_number'      => '08123456778',
            'password'          => bcrypt('p4ssw0rd##'),
            'email_verified_at' => date('Y-m-d H:i')
        ]);
        $superadmin->assignRole(User::ROLE_SUPERUSER);

        $admin = User::create([
            'first_name'        => 'pegawai',
            'email'             => 'pegawai@company.com',
            'phone_number'      => '08123456779',
            'password'          => bcrypt('p4ssw0rd##'),
            'email_verified_at' => date('Y-m-d H:i')
        ]);
        $admin->assignRole(User::ROLE_PEGAWAI);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'user_id'          => 1,
            'favicon'          => null,
            'logo'             => null,
            'name'             => 'Pegawai',
            'short_name'       => 'Pegawai',
            'Description'      => 'Pegawai.',
            'employeecanlogin' => Setting::LOGIN_FALSE
        ]);
    }
}

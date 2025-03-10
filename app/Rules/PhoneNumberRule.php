<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumberRule implements Rule
{
    private $min      = 9;
    private $max      = 13;
    private $prefixes = [
        4 => [
            '62811', '62812', '62813', '62814', '62815', '62816', '62817', '62818', '62819',
            '62821', '62822', '62823', '62828', '62831', '62838', '62851', '62852', '62853',
            '62855', '62856', '62857', '62858', '62859', '62877', '62878', '62879', '62881',
            '62882', '62883', '62884', '62887', '62888', '62889', '62895', '62896', '62897',
            '62898', '62899',
        ],
        5 => ['628681'],
    ];

    public function passes($attribute, $value)
    {
        $normalized = preg_replace('/^08/', '628', $value);
        if(strlen($normalized) < $this->min || strlen($normalized) > $this->max) {
            return false;
        }
        $prefix = substr($normalized, 0, 5);
        foreach($this->prefixes as $length => $validPrefixes) {
            if(in_array($prefix, $validPrefixes)) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return __('Nomor telepon tidak valid atau tidak terdaftar dalam provider yang didukung.');
    }
}

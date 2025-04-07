<?php

namespace App\Services;

use App\Models\License;
use App\Models\RegistrationCertificate;
use Carbon\Carbon;

class ExpiredService
{
    public function updateAllStatuses(): void
    {
        $today         = Carbon::today();
        $thresholdDate = $today->copy()->addDays(180);
        License::query()->each(function($item) use ($today, $thresholdDate) {
            $status = $this->determineStatus($item->validUntil, $today, $thresholdDate);
            $item->update(['status' => $status]);
        });

        RegistrationCertificate::query()->each(function($item) use ($today, $thresholdDate) {
            $status = $this->determineStatus($item->validUntil, $today, $thresholdDate);
            $item->update(['status' => $status]);
        });
    }

    private function determineStatus($validUntil, $today, $thresholdDate): string
    {
        $validUntil = Carbon::parse($validUntil);

        if($validUntil->lessThanOrEqualTo($today)) {
            return License::STATUS_EXPIRED;
        }
        elseif($validUntil->lessThanOrEqualTo($thresholdDate)) {
            return License::STATUS_INACTIVE;
        }

        return License::STATUS_ACTIVE;
    }
}

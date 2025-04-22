<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\License;
use App\Models\RegistrationCertificate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dataType = $request->get('data');
        $status   = $request->get('status');
        $field    = $request->get('field', 'name');
        $order    = $request->get('order', 'asc');
        $perPage  = $request->get('perPage', 10);

        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        $months = collect();
        for($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-M'));
        }

        $maleEmployees   = Employee::where('gender', Employee::GENDER_MALE)->count();
        $femaleEmployees = Employee::where('gender', Employee::GENDER_FEMALE)->count();

        $certificateStats = $this->calculateMonthlyStatus(RegistrationCertificate::class, $months);
        $licenseStats     = $this->calculateMonthlyStatus(License::class, $months);

        $certificatePie = [
            License::STATUS_VALID    => $this->contByStatus(RegistrationCertificate::class, RegistrationCertificate::STATUS_VALID),
            License::STATUS_INACTIVE => $this->contByStatus(RegistrationCertificate::class, RegistrationCertificate::STATUS_INACTIVE),
            License::STATUS_EXPIRED  => $this->contByStatus(RegistrationCertificate::class, RegistrationCertificate::STATUS_EXPIRED),
        ];

        $licensePie = [
            License::STATUS_VALID    => $this->contByStatus(License::class, License::STATUS_VALID),
            License::STATUS_INACTIVE => $this->contByStatus(License::class, License::STATUS_INACTIVE),
            License::STATUS_EXPIRED  => $this->contByStatus(License::class, License::STATUS_EXPIRED),
        ];
        $licenses    = [];
        $certificate = [];
        if($dataType === 'licency') {
            $today = Carbon::today();
            $date  = $today->copy()->addDays(180);

            $query = License::with('employee');

            if($status === License::STATUS_EXPIRED) {
                $query->where('validUntil', '<', $today);
            }
            elseif($status === License::STATUS_INACTIVE) {
                $query->whereBetween('validUntil', [$today, $date]);
            }
            elseif($status === License::STATUS_VALID) {
                $query->where('validUntil', '>', $date);
            }

            $licenses = $query->orderBy($field, $order)->get()->map(function($item) {
                return [
                    'name'               => $item->employee->name ?? '-',
                    'type'               => License::types()[$item->employee->type] ?? '-',
                    'registrationNumber' => $item->registrationNumber,
                    'validFrom'          => $item->validFrom,
                    'validUntil'         => $item->validUntil
                ];
            })->values();
        }
        if($dataType === 'certificate') {
            $today = Carbon::today();
            $date  = $today->copy()->addDays(180);
            $query = RegistrationCertificate::with('employee');
            if($status === License::STATUS_EXPIRED) {
                $query->where('validUntil', '<', $today);
            }
            elseif($status === License::STATUS_INACTIVE) {
                $query->whereBetween('validUntil', [$today, $date]);
            }
            elseif($status === License::STATUS_VALID) {
                $query->where('validUntil', '>', $date);
            }

            $certificate = $query->orderBy($field, $order)->get()->map(function($item) {
                return [
                    'name'               => $item->employee->name ?? '-',
                    'type'               => RegistrationCertificate::types()[$item->employee->type] ?? '-',
                    'registrationNumber' => $item->registrationNumber,
                    'competence'         => $item->competence,
                    'validFrom'          => $item->validFrom,
                    'validUntil'         => $item->validUntil
                ];
            })->values();
        }

        return Inertia::render('Dashboard/Dashboard', [
            'userRole'      => $role,
            'lineChartData' => $this->generateLineChartData($certificateStats, $licenseStats),
            'pieCharts'     => $this->generatePieCharts($maleEmployees, $femaleEmployees, $certificatePie, $licensePie),
            'statuses'      => RegistrationCertificate::statuses(),
            'licenses'      => $licenses,
            'certificates'  => $certificate,
        ]);
    }

    private function contByStatus($model, $status)
    {
        $today = Carbon::today();
        $date  = $today->copy()->addDays(180);

        if($status === License::STATUS_EXPIRED) {
            $count = $model::where('validUntil', '<', $today)->count();
        }
        elseif($status === License::STATUS_INACTIVE) {
            $count = $model::where('validUntil', '<', $date)->where('validUntil', '>', $today)->count();
        }
        elseif($status === License::STATUS_VALID) {
            $count = $model::where('validUntil', '>', $date)->count();
        }
        else {
            $count = 0;
        }

        return $count;
    }

    private function calculateMonthlyStatus($model, $months)
    {
        $result = [
            License::STATUS_VALID    => [],
            License::STATUS_INACTIVE => [],
            License::STATUS_EXPIRED  => [],
        ];

        $today     = Carbon::today();
        $nearLimit = $today->copy()->addDays(180);

        $start = Carbon::parse($months->first())->startOfMonth();
        $end   = Carbon::parse($months->last())->endOfMonth();

        $items = $model::whereDate('validFrom', '<=', $end)->whereDate('validUntil', '>=', $start)->get();

        foreach($months as $month) {
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth   = Carbon::parse($month)->endOfMonth();

            $active   = 0;
            $inactive = 0;
            $expired  = 0;

            foreach($items as $item) {
                $validFrom  = Carbon::parse($item->validFrom);
                $validUntil = Carbon::parse($item->validUntil);

                // Harus valid di bulan ini
                if($validFrom->lte($endOfMonth) && $validUntil->gte($startOfMonth)) {
                    if($validUntil->lt($today)) {
                        $expired++;
                    }
                    elseif($validUntil->lte($nearLimit)) {
                        $inactive++;
                    }
                    else {
                        $active++;
                    }
                }
            }

            $result[License::STATUS_VALID][]    = ['date' => $month, 'total' => $active];
            $result[License::STATUS_INACTIVE][] = ['date' => $month, 'total' => $inactive];
            $result[License::STATUS_EXPIRED][]  = ['date' => $month, 'total' => $expired];
        }

        return $result;
    }

    private function generateLineChartData($certificateStats, $licenseStats)
    {
        return [
            ['name' => __('app.label.valid_certificate'), 'data' => $certificateStats[License::STATUS_VALID]],
            ['name' => __('app.label.expiring_certificate'), 'data' => $certificateStats[License::STATUS_INACTIVE]],
            ['name' => __('app.label.invalid_certificate'), 'data' => $certificateStats[License::STATUS_EXPIRED]],

            ['name' => __('app.label.valid_license'), 'data' => $licenseStats[License::STATUS_VALID]],
            ['name' => __('app.label.expiring_license'), 'data' => $licenseStats[License::STATUS_INACTIVE]],
            ['name' => __('app.label.invalid_license'), 'data' => $licenseStats[License::STATUS_EXPIRED]],
        ];
    }

    private function generatePieCharts($maleEmployees, $femaleEmployees, $certificatePie, $licensePie)
    {
        return [
            'genderDistribution' => [
                ['label' => Employee::genders()[Employee::GENDER_MALE], 'value' => $maleEmployees],
                ['label' => Employee::genders()[Employee::GENDER_FEMALE], 'value' => $femaleEmployees],
            ],
            'certificateStatus'  => [
                [
                    'label' => License::statuses()[License::STATUS_VALID],
                    'value' => $certificatePie[License::STATUS_VALID]
                ],
                [
                    'label' => License::statuses()[License::STATUS_INACTIVE],
                    'value' => $certificatePie[License::STATUS_INACTIVE]
                ],
                [
                    'label' => License::statuses()[License::STATUS_EXPIRED],
                    'value' => $certificatePie[License::STATUS_EXPIRED]
                ],
            ],
            'licenseStatus'      => [
                [
                    'label' => RegistrationCertificate::statuses()[RegistrationCertificate::STATUS_VALID],
                    'value' => $licensePie[RegistrationCertificate::STATUS_VALID]
                ],
                [
                    'label' => RegistrationCertificate::statuses()[RegistrationCertificate::STATUS_INACTIVE],
                    'value' => $licensePie[RegistrationCertificate::STATUS_INACTIVE]
                ],
                [
                    'label' => RegistrationCertificate::statuses()[RegistrationCertificate::STATUS_EXPIRED],
                    'value' => $licensePie[RegistrationCertificate::STATUS_EXPIRED]
                ],
            ],
        ];
    }

}

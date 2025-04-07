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
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $maleEmployees   = Employee::where('gender', 'male')->count();
        $femaleEmployees = Employee::where('gender', 'female')->count();

        $certificateStats = $this->calculateMonthlyStatus(RegistrationCertificate::class, $months);
        $licenseStats     = $this->calculateMonthlyStatus(License::class, $months);
        $certificatePie   = [
            'active'   => RegistrationCertificate::where('status', RegistrationCertificate::STATUS_ACTIVE)->count(),
            'inactive' => RegistrationCertificate::where('status', RegistrationCertificate::STATUS_INACTIVE)->count(),
            'expired'  => RegistrationCertificate::where('status', RegistrationCertificate::STATUS_EXPIRED)->count(),
        ];
        $licensePie       = [
            'active'   => License::where('status', License::STATUS_ACTIVE)->count(),
            'inactive' => License::where('status', License::STATUS_INACTIVE)->count(),
            'expired'  => License::where('status', License::STATUS_EXPIRED)->count(),
        ];

        $licenses    = [];
        $certificate = [];
        if($dataType === 'licency') {
            $query = License::with('employee')->where('status', $status);
            $query->orderBy($field, $order);
            $licenses = $query->get()->map(function($item) {
                return [
                    'name'               => $item->employee->name ?? '-',
                    'type'               => License::types()[$item->type],
                    'registrationNumber' => $item->registrationNumber,
                    'validFrom'          => $item->validFrom,
                    'validUntil'         => $item->validUntil
                ];
            })->values();
        }
        if($dataType === 'certificate') {
            $query = RegistrationCertificate::with('employee')->where('status', $status);
            $query->orderBy($field, $order);
            $certificate = $query->get()->map(function($item) {
                return [
                    'name'               => $item->employee->name ?? '-',
                    'type'               => RegistrationCertificate::types()[$item->type],
                    'registrationNumber' => $item->registrationNumber,
                    'competence'         => $item->competence,
                    'validFrom'          => $item->validFrom,
                    'validUntil'         => $item->validUntil
                ];
            })->values();
        }

        return Inertia::render('Dashboard/Dashboard', [
            'userRole'      => $role,
            'lineChartData' => [
                ['name' => 'Sertifikat Berlaku', 'data' => $certificateStats['active']],
                ['name' => 'Sertifikat Akan Kadaluarsa', 'data' => $certificateStats['inactive']],
                ['name' => 'Sertifikat Tidak Berlaku', 'data' => $certificateStats['expired']],
                ['name' => 'Izin Berlaku', 'data' => $licenseStats['active']],
                ['name' => 'Izin Akan Kadaluarsa', 'data' => $licenseStats['inactive']],
                ['name' => 'Izin Tidak Berlaku', 'data' => $licenseStats['expired']],
            ],
            'pieCharts'     => [
                'genderDistribution' => [
                    ['label' => 'Laki-laki', 'value' => $maleEmployees],
                    ['label' => 'Perempuan', 'value' => $femaleEmployees],
                ],
                'certificateStatus'  => [
                    ['label' => 'Berlaku', 'value' => $certificatePie['active']],
                    ['label' => 'Akan Kadaluarsa', 'value' => $certificatePie['inactive']],
                    ['label' => 'Tidak Berlaku', 'value' => $certificatePie['expired']],
                ],
                'licenseStatus'      => [
                    ['label' => 'Berlaku', 'value' => $licensePie['active']],
                    ['label' => 'Akan Kadaluarsa', 'value' => $licensePie['inactive']],
                    ['label' => 'Tidak Berlaku', 'value' => $licensePie['expired']],
                ],
            ],
            'licenses'      => $licenses,
            'certificates'  => $certificate,
        ]);
    }

    private function calculateMonthlyStatus($model, $months)
    {
        $result = [
            'active'   => [],
            'inactive' => [],
            'expired'  => [],
        ];

        foreach($months as $month) {
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth   = Carbon::parse($month)->endOfMonth();

            $active = $model::where('status', $model::STATUS_ACTIVE)->whereDate('validFrom', '<=', $endOfMonth)
                ->whereDate('validUntil', '>=', $startOfMonth)->count();

            $inactive = $model::where('status', $model::STATUS_ACTIVE)->whereDate('validUntil', '>', $endOfMonth)
                ->whereDate('validUntil', '<=', $endOfMonth->copy()->addDays(30))->count();

            $expired = $model::whereDate('validUntil', '<', $startOfMonth)->count();

            $result['active'][]   = ['date' => $month, 'total' => $active];
            $result['inactive'][] = ['date' => $month, 'total' => $inactive];
            $result['expired'][]  = ['date' => $month, 'total' => $expired];
        }

        return $result;
    }

    public function employee(Request $request)
    {
        $perPage = (int)$request->input('perPage', 10);
        $user    = auth()->user();
        $role    = $user->getRoleNames()->first();

        $startDate         = $request->query('start_date', now()->subDays(6)->toDateString());
        $endDate           = $request->query('end_date', now()->toDateString());
        $maleEmployees     = Employee::where('gender', 'male')->count();
        $femaleEmployees   = Employee::where('gender', 'female')->count();
        $activeEmployees   = Employee::where('status', Employee::STATUS_ACTIVE)->count();
        $inactiveEmployees = Employee::where('status', Employee::STATUS_INACTIVE)->count();
        $dates             = collect();
        for($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->toDateString());
        }

        $initialEmployeeCount = Employee::where('status', Employee::STATUS_ACTIVE)->count();

        $employeesRaw = Employee::where('status', Employee::STATUS_ACTIVE)->whereBetween('created_at', [
            $startDate,
            $endDate
        ])->selectRaw('DATE(created_at) as date, COUNT(*) as total')->groupBy('date')->pluck('total', 'date')
            ->toArray();

        $formattedEmployees = [];
        $cumulativeTotal    = $initialEmployeeCount;
        foreach($dates as $date) {
            $cumulativeTotal      += $employeesRaw[$date] ?? 0;
            $formattedEmployees[] = ['date' => $date, 'total' => $cumulativeTotal];
        }

        $employeeTable = Employee::query()->when($request->filled('search'), function($query) use ($request) {
            $search = "%" . $request->search . "%";
            $query->where(function($q) use ($search) {
                $q->where('created_at', 'LIKE', $search)->orWhere('code', 'LIKE', $search)
                    ->orWhere('name', 'LIKE', $search)->orWhere('gender', 'LIKE', $search);
            });
        })->when($request->filled(['field', 'order']), function($query) use ($request) {
            $query->orderBy($request->field, $request->order);
        })->paginate($perPage)->onEachSide(0);


        return Inertia::render('Dashboard/Employee', [
            'userRole'      => $role,
            'title'         => __('app.label.employee'),
            'perPage'       => (int)$perPage,
            'filters'       => $request->only(['search', 'field', 'order']),
            'pieCharts'     => [
                'genderDistribution' => [
                    ['label' => 'Laki-laki', 'value' => $maleEmployees],
                    ['label' => 'Perempuan', 'value' => $femaleEmployees],
                ],
                'statusDistribution' => [
                    ['label' => 'Aktif', 'value' => $activeEmployees],
                    ['label' => 'Non-Aktif', 'value' => $inactiveEmployees],
                ],
            ],
            'lineChartData' => [
                ['name' => 'Total Pegawai', 'data' => $formattedEmployees],
            ],
            'genders'       => Employee::genders(),
            'statuses'      => Employee::statuses(),
            'employeeTable' => $employeeTable,
        ]);
    }

    public function certificates(Request $request)
    {
        $perPage = (int)$request->input('perPage', 10);
        $user    = auth()->user();
        $role    = $user->getRoleNames()->first();

        $validCertificates            = RegistrationCertificate::where('status', RegistrationCertificate::STATUS_ACTIVE)
            ->whereDate('validUntil', '>=', now())->count();
        $expiredCertificates          = RegistrationCertificate::where('status', RegistrationCertificate::STATUS_ACTIVE)
            ->whereDate('validUntil', '<', now())->count();
        $employeesWithCertificates    = Employee::whereHas('certificates')->count();
        $employeesWithoutCertificates = Employee::doesntHave('certificates')->count();
        $dates                        = collect();
        for($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->toDateString());
        }
        $formattedCertificates = $this->calculateCumulativeData(RegistrationCertificate::class, 'validFrom', 'validUntil', $dates);
        $certificateTable      = RegistrationCertificate::with('employee')
            ->when($request->filled('search'), function($query) use ($request) {
                $search = "%" . $request->search . "%";
                $query->where(function($q) use ($search) {
                    $q->where('created_at', 'LIKE', $search)->orWhere('status', 'LIKE', $search);
                })->orWhereHas('employee', function($q) use ($search) {
                    $q->where('name', 'LIKE', $search)->orWhere('code', 'LIKE', $search);
                });
            })->when($request->filled(['field', 'order']), function($query) use ($request) {
                $query->orderBy($request->field, $request->order);
            })->paginate($perPage)->onEachSide(0);


        return Inertia::render('Dashboard/Certificate', [
            'userRole'         => $role,
            'title'            => __('app.label.certificate'),
            'perPage'          => (int)$perPage,
            'filters'          => $request->only(['search', 'field', 'order']),
            'pieCharts'        => [
                'certificateValidity'  => [
                    ['label' => 'Sertifikat Berlaku', 'value' => $validCertificates],
                    ['label' => 'Sertifikat Kadaluarsa', 'value' => $expiredCertificates],
                ],
                'certificateOwnership' => [
                    ['label' => 'Memiliki Sertifikat', 'value' => $employeesWithCertificates],
                    ['label' => 'Belum Memiliki Sertifikat', 'value' => $employeesWithoutCertificates],
                ],
            ],
            'lineChartData'    => [
                ['name' => 'Total Surat Tanda Registrasi', 'data' => $formattedCertificates],
            ],
            'certificateTable' => $certificateTable,
            'types'            => RegistrationCertificate::types(),
            'statuses'         => RegistrationCertificate::statuses(),
        ]);
    }

    public function licenses(Request $request)
    {
        $perPage                  = (int)$request->input('perPage', 10);
        $user                     = auth()->user();
        $role                     = $user->getRoleNames()->first();
        $validLicenses            = License::where('status', License::STATUS_ACTIVE)
            ->whereDate('validUntil', '>=', now())->count();
        $expiredLicenses          = License::where('status', License::STATUS_ACTIVE)
            ->whereDate('validUntil', '<', now())->count();
        $employeesWithLicenses    = Employee::whereHas('licenses')->count();
        $employeesWithoutLicenses = Employee::doesntHave('licenses')->count();

        $dates = collect();
        for($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->toDateString());
        }
        $formattedLicenses = $this->calculateCumulativeData(License::class, 'validFrom', 'validUntil', $dates);
        $licenseTable      = License::with('employee')->when($request->filled('search'), function($query) use (
            $request
        ) {
            $search = "%" . $request->search . "%";
            $query->where(function($q) use ($search) {
                $q->where('created_at', 'LIKE', $search)->orWhere('status', 'LIKE', $search);
            })->orWhereHas('employee', function($q) use ($search) {
                $q->where('name', 'LIKE', $search)->orWhere('code', 'LIKE', $search);
            });
        })->when($request->filled(['field', 'order']), function($query) use ($request) {
            $query->orderBy($request->field, $request->order);
        })->paginate($perPage)->onEachSide(0);

        return Inertia::render('Dashboard/License', [
            'userRole'      => $role,
            'title'         => __('app.label.license'),
            'perPage'       => (int)$perPage,
            'filters'       => $request->only(['search', 'field', 'order']),
            'pieCharts'     => [
                'licenseValidity'  => [
                    ['label' => 'Lisensi Berlaku', 'value' => $validLicenses],
                    ['label' => 'Lisensi Kadaluarsa', 'value' => $expiredLicenses],
                ],
                'licenseOwnership' => [
                    ['label' => 'Memiliki Lisensi', 'value' => $employeesWithLicenses],
                    ['label' => 'Belum Memiliki Lisensi', 'value' => $employeesWithoutLicenses],
                ],
            ],
            'lineChartData' => [
                ['name' => 'Total Employee Licenses', 'data' => $formattedLicenses],
            ],
            'licenseTable'  => $licenseTable,
            'types'         => License::types(),
            'statuses'      => License::statuses(),
        ]);
    }


}

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\License;
use App\Models\RegistrationCertificate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = auth()->user();
        $role  = $user->getRoleNames()->first();
        $dates = collect();
        for($i = 6; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->toDateString());
        }

        $initialEmployeeCount = Employee::where('status', Employee::STATUS_ACTIVE)
            ->whereDate('created_at', '<', now()->subDays(6))->count();

        $employeesRaw = Employee::where('status', Employee::STATUS_ACTIVE)
            ->whereDate('created_at', '>=', now()->subDays(6))->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')->pluck('total', 'date')->toArray();

        $formattedEmployees = [];
        $cumulativeTotal    = $initialEmployeeCount;
        foreach($dates as $date) {
            $cumulativeTotal      += $employeesRaw[$date] ?? 0;
            $formattedEmployees[] = ['date' => $date, 'total' => $cumulativeTotal];
        }

        $formattedCertificates = $this->calculateCumulativeData(RegistrationCertificate::class, 'validFrom', 'validUntil', $dates);
        $formattedLicenses     = $this->calculateCumulativeData(License::class, 'validFrom', 'validUntil', $dates);
        $maleEmployees         = Employee::where('gender', 'male')->count();
        $femaleEmployees       = Employee::where('gender', 'female')->count();
        $validCertificates     = RegistrationCertificate::where('status', RegistrationCertificate::STATUS_ACTIVE)
            ->whereDate('validUntil', '>=', now())->distinct('employeeId')->count();
        $expiredCertificates   = RegistrationCertificate::where('status', RegistrationCertificate::STATUS_ACTIVE)
            ->whereDate('validUntil', '<', now())->distinct('employeeId')->count();
        $validLicenses         = License::where('status', License::STATUS_ACTIVE)->whereDate('validUntil', '>=', now())
            ->distinct('employeeId')->count();
        $expiredLicenses       = License::where('status', License::STATUS_ACTIVE)->whereDate('validUntil', '<', now())
            ->distinct('employeeId')->count();

        $pieCharts = [
            'genderDistribution'  => [
                ['label' => 'Laki-laki', 'value' => $maleEmployees],
                ['label' => 'Perempuan', 'value' => $femaleEmployees],
            ],
            'certificateValidity' => [
                ['label' => 'Sertifikat Berlaku', 'value' => $validCertificates],
                ['label' => 'Sertifikat Kadaluarsa', 'value' => $expiredCertificates],
            ],
            'licenseValidity'     => [
                ['label' => 'Izin Berlaku', 'value' => $validLicenses],
                ['label' => 'Izin Kadaluarsa', 'value' => $expiredLicenses],
            ],
        ];


        return Inertia::render('Dashboard/Dashboard', [
            'userRole'      => $role,
            'lineChartData' => [
                ['name' => 'Total Employee', 'data' => $formattedEmployees],
                ['name' => 'Total Registration Certificate', 'data' => $formattedCertificates],
                ['name' => 'Total License', 'data' => $formattedLicenses],
            ],
            'pieCharts'     => $pieCharts,
        ]);
    }

    private function calculateCumulativeData($model, $startColumn, $endColumn, $dates)
    {
        $initialCount    = $model::where('status', $model::STATUS_ACTIVE)->count();
        $rawData         = $model::where('status', $model::STATUS_ACTIVE)
            ->whereDate($endColumn, '>=', now()->subDays(6))
            ->selectRaw("DATE($startColumn) as date_start, DATE($endColumn) as date_end, COUNT(*) as total")
            ->groupBy('date_start', 'date_end')->orderBy('date_start')->get();
        $formattedData   = [];
        $cumulativeCount = $initialCount;

        foreach($dates as $date) {
            foreach($rawData as $data) {
                if($data->date_start == $date) {
                    $cumulativeCount += $data->total;
                }
                if($data->date_end == $date) {
                    $cumulativeCount -= $data->total;
                }
            }
            $formattedData[] = ['date' => $date, 'total' => max(0, $cumulativeCount)];
        }

        return $formattedData;
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
                ['name' => 'Total Employee', 'data' => $formattedEmployees],
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
                ['name' => 'Total Registration Certificate', 'data' => $formattedCertificates],
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

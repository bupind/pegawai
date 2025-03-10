<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        return Inertia::render('Dashboard/Dashboard', [
            'userRole' => $role
        ]);
    }
}

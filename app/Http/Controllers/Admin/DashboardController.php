<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ImportRun;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'total'       => Employee::count(),
            'active'      => Employee::active()->count(),
            'inactive'    => Employee::where('is_active', false)->count(),
            'lastImport'  => ImportRun::latest('created_at')->first(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Employee::class);

        $query = Employee::query()->orderBy('last_name')->orderBy('first_name');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q
                ->where('first_name', 'like', $s)
                ->orWhere('last_name', 'like', $s)
                ->orWhere('email', 'like', $s)
                ->orWhere('job_title', 'like', $s)
                ->orWhere('department', 'like', $s)
            );
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        return view('admin.employees.index', [
            'employees' => $query->paginate(25)->withQueryString(),
        ]);
    }

    public function show(Employee $employee): View
    {
        $this->authorize('view', $employee);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->authorize('update', $employee);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'local');
            $data['photo_path'] = $path;
        }

        unset($data['photo']);
        $employee->update($data);

        $this->audit->log($request->user(), 'employee.updated', $employee, ['fields' => array_keys($data)]);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Collaborateur mis à jour.');
    }

    public function toggleActive(Request $request, Employee $employee): RedirectResponse
    {
        $this->authorize('toggleActive', $employee);

        $employee->update([
            'is_active'      => !$employee->is_active,
            'deactivated_at' => $employee->is_active ? now() : null,
        ]);

        $action = $employee->is_active ? 'employee.activated' : 'employee.deactivated';
        $this->audit->log($request->user(), $action, $employee);

        return back()->with('success', $employee->is_active ? 'Carte activée.' : 'Carte désactivée.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $departments = Department::withCount('employees')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(10);

        $stats = [
            'total' => Department::count(),
            'total_employees' => Employee::count(),
            'active' => Department::where('status', 'active')->count(),
            'inactive' => Department::where('status', 'inactive')->count(),
        ];

        return view('admin.settings.departments', compact('departments', 'stats'));
    }

    public function show(Department $department): View
    {
        $department->load(['employees.branch']);

        $stats = [
            'total_employees' => $department->employees->count(),
            'active' => $department->employees->where('status', 'active')->count(),
            'inactive' => $department->employees->where('status', 'inactive')->count(),
            'on_leave' => $department->employees->where('status', 'on_leave')->count(),
        ];

        return view('admin.settings.department_view', compact('department', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'status' => 'required|in:active,inactive',
        ]);

        Department::create($validated);

        return redirect()->back()->with([
            'message' => 'Department created successfully',
            'title' => 'Department Created',
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,'.$department->id,
            'status' => 'required|in:active,inactive',
        ]);

        $department->update($validated);

        return redirect()->back()->with([
            'message' => 'Department updated successfully',
            'title' => 'Department Updated',
            'alert-type' => 'success',
        ]);
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->employees()->count() > 0) {
            return redirect()->back()->with([
                'message' => 'Cannot delete department with active employees',
                'title' => 'Deletion Denied',
                'alert-type' => 'error',
            ]);
        }

        $department->delete();

        return redirect()->back()->with([
            'message' => 'Department deleted successfully',
            'title' => 'Department Deleted',
            'alert-type' => 'success',
        ]);
    }
}

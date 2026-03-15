<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $department_id = $request->input('department_id');
        $status = $request->input('status');

        $employees = Employee::with(['department', 'branch'])
            ->when($search, function ($query) use ($search) {
                $query->whereNested(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('employee_id', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($department_id, function ($query) use ($department_id) {
                $query->where('department_id', $department_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'on_leave' => Employee::where('status', 'on_leave')->count(),
            'departments' => Department::count(),
        ];

        $departments = Department::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        // Seed default departments if none exist for a better first experience
        if ($departments->isEmpty()) {
            foreach (['Sales', 'Accounting', 'IT', 'Operations', 'HR', 'Marketing', 'Legal'] as $dept) {
                Department::create(['name' => $dept]);
            }
            $departments = Department::orderBy('name')->get();
        }

        return view('admin.settings.employees', compact('employees', 'stats', 'departments', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id',
            'name' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'join_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,on_leave',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('employees', 'public');
            $validated['profile_image'] = $imagePath;
        }

        Employee::create($validated);

        return redirect()->back()->with([
            'message' => 'Employee created successfully',
            'title' => 'Employee Created',
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:employees,employee_id,'.$employee->id,
            'name' => 'required',
            'email' => 'required|email|unique:employees,email,'.$employee->id,
            'phone' => 'required',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required',
            'branch_id' => 'required|exists:branches,id',
            'join_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'status' => 'required|in:active,inactive,on_leave',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relationship' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($employee->profile_image) {
                Storage::disk('public')->delete($employee->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('employees', 'public');
            $validated['profile_image'] = $imagePath;
        }

        $employee->update($validated);

        return redirect()->back()->with([
            'message' => 'Employee updated successfully',
            'title' => 'Employee Updated',
            'alert-type' => 'success',
        ]);
    }

    public function show(Employee $employee): View
    {
        $employee->load(['department', 'branch', 'user.role.permissions']);
        $departments = Department::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('admin.settings.employee_view', compact('employee', 'departments', 'branches'));
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->back()->with([
            'message' => 'Employee deleted successfully',
            'title' => 'Employee Deleted',
            'alert-type' => 'success',
        ]);
    }
}

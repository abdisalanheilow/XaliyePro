<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRoleController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $users = User::with(['role', 'branches.stores', 'stores'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(10, ['*'], 'users_page');

        $roles = Role::withCount(['users', 'permissions'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10, ['*'], 'roles_page');

        $permissions = Permission::all()->groupBy('module');
        $branches = Branch::with('stores')->get();
        $employees = Employee::all();

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'Active')->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
        ];

        return view('admin.settings.users_roles', compact('users', 'roles', 'permissions', 'branches', 'employees', 'stats'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id|unique:users,employee_id',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
            'view_all_branches' => $request->has('view_all_branches'),
        ]);

        if ($request->has('branches')) {
            $user->branches()->sync($request->branches);
        }

        if ($request->has('stores')) {
            $user->stores()->sync($request->stores);
        }

        return redirect()->route('settings.users.index')->with([
            'message' => 'User created successfully!',
            'title' => 'User Created',
            'alert-type' => 'success',
        ]);
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:Active,Inactive',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'employee_id' => $request->employee_id,
            'status' => $request->status,
            'view_all_branches' => $request->has('view_all_branches'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sync branches and stores as part of universal update
        $user->branches()->sync($request->branches ?? []);
        $user->stores()->sync($request->stores ?? []);

        return redirect()->route('settings.users.index')->with([
            'message' => 'User updated successfully!',
            'title' => 'User Updated',
            'alert-type' => 'success',
        ]);
    }

    public function showUser(User $user): View
    {
        $user->load(['role', 'branches.stores', 'stores', 'employee']);

        return view('admin.settings.user_view', compact('user'));
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'required',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'branch_access_type' => $request->has('branch_access_type') ? 'restricted' : 'all',
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('settings.users.index', ['tab' => 'roles'])->with([
            'message' => 'Role created successfully!',
            'title' => 'Role Created',
            'alert-type' => 'success',
        ]);
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'description' => 'required',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'branch_access_type' => $request->has('branch_access_type') ? 'restricted' : 'all',
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('settings.users.index', ['tab' => 'roles'])->with([
            'message' => 'Role updated successfully!',
            'title' => 'Role Updated',
            'alert-type' => 'success',
        ]);
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('settings.users.index', ['tab' => 'roles'])->with([
            'message' => 'Role deleted successfully!',
            'title' => 'Role Deleted',
            'alert-type' => 'success',
        ]);
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users.index')->with([
                'message' => 'You cannot delete yourself!',
                'title' => 'Deletion Denied',
                'alert-type' => 'error',
            ]);
        }

        $user->delete();

        return redirect()->route('settings.users.index')->with([
            'message' => 'User deleted successfully!',
            'title' => 'User Deleted',
            'alert-type' => 'success',
        ]);
    }
}

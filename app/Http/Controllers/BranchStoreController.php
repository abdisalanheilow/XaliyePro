<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BranchStoreController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $branches = Branch::with([
            'stores' => function ($query) use ($search, $status) {
                if ($search) {
                    $query->whereNested(function (Builder $q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                }
                if ($status) {
                    $query->where('status', $status);
                }
            },
        ])
            ->when($search, function ($query) use ($search) {
                $query->whereNested(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhereHas('stores', function (Builder $sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->paginate(10);

        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        // Calculate next branch code
        /** @var \App\Models\Branch $lastBranch */
        $lastBranch = Branch::orderBy('id', 'desc')->first();
        $nextBranchCode = 'BR-'.str_pad(($lastBranch ? $lastBranch->id + 1 : 1), 3, '0', STR_PAD_LEFT);

        // Calculate next store code
        /** @var \App\Models\Store $lastStore */
        $lastStore = Store::orderBy('id', 'desc')->first();
        $nextStoreCode = 'ST-'.str_pad(($lastStore ? $lastStore->id + 1 : 1), 3, '0', STR_PAD_LEFT);

        return view('admin.settings.branches_stores', compact('branches', 'employees', 'nextBranchCode', 'nextStoreCode'));
    }

    public function showBranch(Branch $branch): View
    {
        $branch->load('stores');

        return view('admin.settings.branch_view', compact('branch'));
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|unique:branches,code',
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'manager_name' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        Branch::create($validated);

        return redirect()->back()->with([
            'message' => 'Branch created successfully!',
            'title' => 'Branch Created',
            'alert-type' => 'success',
        ]);
    }

    public function updateBranch(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|unique:branches,code,'.$branch->id,
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email',
            'manager_name' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($validated);

        return redirect()->back()->with([
            'message' => 'Branch updated successfully!',
            'title' => 'Branch Updated',
            'alert-type' => 'success',
        ]);
    }

    public function destroyBranch(Branch $branch): RedirectResponse
    {
        $branch->delete();

        return redirect()->back()->with([
            'message' => 'Branch deleted successfully',
            'title' => 'Branch Deleted',
            'alert-type' => 'success',
        ]);
    }

    public function storeStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'code' => 'required|unique:stores,code',
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable',
            'manager_name' => 'nullable',
            'employee_count' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Store::create($validated);

        return redirect()->back()->with([
            'message' => 'Store created successfully!',
            'title' => 'Store Created',
            'alert-type' => 'success',
        ]);
    }

    public function updateStore(Request $request, Store $store): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'code' => 'required|unique:stores,code,'.$store->id,
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable',
            'manager_name' => 'nullable',
            'employee_count' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $store->update($validated);

        return redirect()->back()->with([
            'message' => 'Store updated successfully!',
            'title' => 'Store Updated',
            'alert-type' => 'success',
        ]);
    }

    public function destroyStore(Store $store): RedirectResponse
    {
        $store->delete();

        return redirect()->back()->with([
            'message' => 'Store deleted successfully',
            'title' => 'Store Deleted',
            'alert-type' => 'success',
        ]);
    }
}

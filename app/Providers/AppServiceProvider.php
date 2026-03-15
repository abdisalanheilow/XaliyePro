<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.body.header', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // Get accessible branches
                $branches = $user->view_all_branches
                    ? Branch::with('stores')->get()
                    : $user->branches()->with('stores')->get();

                // Get current selection from session, fallback to default
                $currentBranchId = session('active_branch_id', $user->default_branch_id);
                $currentStoreId = session('active_store_id');

                // If no branch in session or default, take first available
                if (! $currentBranchId && $branches->count() > 0) {
                    $currentBranchId = $branches->first()->id;
                }

                // If no store in session, take first available from active branch
                if (! $currentStoreId && $currentBranchId) {
                    $branch = $branches->where('id', $currentBranchId)->first();
                    if ($branch && $branch->stores->count() > 0) {
                        $currentStoreId = $branch->stores->first()->id;
                    }
                }

                // Sync session if modified
                session([
                    'active_branch_id' => $currentBranchId,
                    'active_store_id' => $currentStoreId,
                    'view_all_branches' => session('view_all_branches', false),
                ]);

                $view->with([
                    'accessibleBranches' => $branches,
                    'activeBranchId' => $currentBranchId,
                    'activeStoreId' => $currentStoreId,
                ]);
            }
        });
        View::composer(['admin.admin_master', 'admin.body.sidebar'], function ($view) {
            $companySettings = CompanySetting::first();
            $view->with('companySettings', $companySettings);
            // Backward compatibility for sidebar if it uses singular
            $view->with('companySetting', $companySettings);
        });
    }
}

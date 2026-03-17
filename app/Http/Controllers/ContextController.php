<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContextController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($request->has('active_branch_id')) {
            session(['active_branch_id' => $request->active_branch_id]);

            // Re-validate store if branch changed
            session()->forget('active_store_id');
        }

        if ($request->has('active_store_id')) {
            session(['active_store_id' => $request->active_store_id]);
        }

        if ($request->has('view_all_branches')) {
            // Only allow if user has the permission
            if ($user->view_all_branches) {
                session(['view_all_branches' => filter_var($request->view_all_branches, FILTER_VALIDATE_BOOLEAN)]);
            }
        }

        return response()->json(['success' => true]);
    }
}

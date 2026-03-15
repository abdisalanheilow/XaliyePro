<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index(Request $request): View
    {
        $query = Unit::query()->with('baseUnit');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereAny(['name', 'short_name'], 'LIKE', "%{$search}%");
        }

        // Filter by Status
        if ($request->has('status') && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        $units = $query->latest()->paginate(10);
        $baseUnits = Unit::where('status', 'active')->whereNull('base_unit_id')->get(); // Only top-level units can be base units

        $stats = [
            'total' => Unit::count(),
            'active' => Unit::where('status', 'active')->count(),
            'total_items' => Item::count(),
            'avg_per_unit' => Unit::count() > 0 ? Item::count() / Unit::count() : 0,
        ];

        return view('admin.items.units', compact('units', 'stats', 'baseUnits'));
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'short_name' => 'required|string|max:20',
            'base_unit_id' => 'nullable|exists:units,id',
            'operator' => 'nullable|string',
            'operation_value' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        Unit::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'base_unit_id' => $request->base_unit_id,
            'operator' => $request->operator,
            'operation_value' => $request->operation_value,
            'status' => $request->status,
        ]);

        return redirect()->back()->with([
            'message' => 'Unit Created Successfully',
            'title' => 'Unit Created',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,'.$id,
            'short_name' => 'required|string|max:20',
            'base_unit_id' => 'nullable|exists:units,id',
            'operator' => 'nullable|string',
            'operation_value' => 'nullable|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        $unit->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'base_unit_id' => $request->base_unit_id,
            'operator' => $request->operator,
            'operation_value' => $request->operation_value,
            'status' => $request->status,
        ]);

        return redirect()->back()->with([
            'message' => 'Unit Updated Successfully',
            'title' => 'Unit Updated',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified unit.
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit->items()->exists()) {
            return redirect()->back()->with([
                'message' => 'Cannot delete unit of measurement. It is assigned to items.',
                'alert-type' => 'error',
            ]);
        }

        $unit->delete();

        $notification = [
            'message' => 'Unit Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    /**
     * Download CSV Template for Import.
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="units_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID (Optional)', 'Name', 'Short Name', 'Base Unit (Optional)', 'Operator (Optional)', 'Operator Value (Optional)', 'Status (active/inactive)']);
            fputcsv($file, ['', 'Kilogram', 'kg', '', '*', '1', 'active']);
            fputcsv($file, ['', 'Gram', 'g', 'Kilogram', '/', '1000', 'active']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export units to CSV.
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="units.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Short Name', 'Base Unit', 'Operator', 'Operator Value', 'Status']);

            Unit::chunk(100, function ($units) use ($file) {
                foreach ($units as $unit) {
                    fputcsv($file, [
                        $unit->id,
                        $unit->name,
                        $unit->short_name,
                        $unit->base_unit,
                        $unit->operator,
                        $unit->operator_value,
                        $unit->status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import units from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            $importedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 7 && ! empty(trim($row[1])) && ! empty(trim($row[2]))) {
                    $name = trim($row[1]);
                    $shortName = trim($row[2]);
                    $baseUnit = ! empty(trim($row[3])) ? trim($row[3]) : null;
                    $operator = ! empty(trim($row[4])) ? trim($row[4]) : '*';
                    $operatorValue = ! empty(trim($row[5])) ? trim($row[5]) : null;
                    $status = trim(strtolower($row[6]));
                    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';

                    Unit::updateOrCreate(
                        ['name' => $name],
                        [
                            'short_name' => $shortName,
                            'base_unit' => $baseUnit,
                            'operator' => in_array($operator, ['*', '/']) ? $operator : '*',
                            'operator_value' => is_numeric($operatorValue) ? $operatorValue : null,
                            'status' => $status,
                        ]
                    );

                    $importedCount++;
                }
            }

            fclose($handle);
        }

        return redirect()->back()->with([
            'message' => "Successfully imported $importedCount units.",
            'alert-type' => 'success',
        ]);
    }
}

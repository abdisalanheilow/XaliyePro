<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = Brand::query();

        if ($request->search) {
            $search = $request->search;
            $query->whereAny(['name', 'description'], 'LIKE', "%{$search}%");
        }

        $brands = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Brand::count(),
            'active' => Brand::where('status', 'active')->count(),
            'inactive' => Brand::where('status', 'inactive')->count(),
        ];

        return view('admin.items.brands', compact('brands', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($request->name);
        Brand::create($validated);

        return redirect()->back()->with([
            'message' => 'Brand Added Successfully',
            'title' => 'Brand Created',
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['slug'] = Str::slug($request->name);
        $brand->update($validated);

        return redirect()->back()->with([
            'message' => 'Brand Updated Successfully',
            'title' => 'Brand Updated',
            'alert-type' => 'success',
        ]);
    }

    public function destroy($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);

        if ($brand->items()->exists()) {
            return redirect()->back()->with([
                'message' => 'Cannot delete brand. It is assigned to items.',
                'title' => 'Deletion Denied',
                'alert-type' => 'error',
            ]);
        }

        $brand->delete();

        return redirect()->back()->with([
            'message' => 'Brand Deleted Successfully',
            'title' => 'Brand Deleted',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Download CSV Template for Import.
     */
    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="brands_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID (Optional)', 'Name', 'Slug (Optional)', 'Description (Optional)', 'Status (active/inactive)']);
            fputcsv($file, ['', 'Apple', 'apple', 'Apple Electronics', 'active']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export brands to CSV.
     */
    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="brands.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Slug', 'Description', 'Status']);

            Brand::chunk(100, function ($brands) use ($file) {
                foreach ($brands as $brand) {
                    fputcsv($file, [
                        $brand->id,
                        $brand->name,
                        $brand->slug,
                        $brand->description,
                        $brand->status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import brands from CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            $importedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 5 && ! empty(trim($row[1]))) {
                    $name = trim($row[1]);
                    $slug = empty(trim($row[2])) ? Str::slug($name) : trim($row[2]);
                    $description = trim($row[3]);
                    $status = trim(strtolower($row[4]));
                    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';

                    Brand::updateOrCreate(
                        ['name' => $name],
                        [
                            'slug' => $slug,
                            'description' => $description,
                            'status' => $status,
                        ]
                    );

                    $importedCount++;
                }
            }

            fclose($handle);
        }

        return redirect()->back()->with([
            'message' => "Successfully imported $importedCount brands.",
            'title' => 'Import Complete',
            'alert-type' => 'success',
        ]);
    }
}

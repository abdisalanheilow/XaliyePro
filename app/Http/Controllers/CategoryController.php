<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $query = Category::query();

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->whereAny(['name', 'description'], 'LIKE', "%{$search}%");
        }

        // Filter by Type
        if ($request->type && $request->type !== 'All Types') {
            $query->where('type', strtolower($request->type));
        }

        // Filter by Status
        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        $categories = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Category::count(),
            'active' => Category::where('status', 'active')->count(),
            'inactive' => Category::where('status', 'inactive')->count(),
            'product_count' => Category::where('type', 'product')->count(),
            'service_count' => Category::where('type', 'service')->count(),
            'total_items' => Item::count(),
        ];

        return view('admin.items.categories', compact('categories', 'stats'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'type' => 'required|in:product,service',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $categoryData = $validated;
        $categoryData['slug'] = Str::slug($request->name);

        Category::create($categoryData);

        return redirect()->back()->with([
            'message' => 'Category Created Successfully',
            'title' => 'Category Created',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
            'type' => 'required|in:product,service',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $categoryData = $validated;
        $categoryData['slug'] = Str::slug($request->name);

        $category->update($categoryData);

        return redirect()->back()->with([
            'message' => 'Category Updated Successfully',
            'title' => 'Category Updated',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->items()->exists()) {
            return redirect()->back()->with([
                'message' => 'Cannot delete category. It is assigned to items.',
                'alert-type' => 'error',
            ]);
        }

        $category->delete();

        $notification = [
            'message' => 'Category Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    /**
     * Export categories to CSV.
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Type', 'Slug', 'Description', 'Status']);

            Category::chunk(100, function ($categories) use ($file) {
                foreach ($categories as $category) {
                    fputcsv($file, [
                        $category->id,
                        $category->name,
                        $category->type,
                        $category->slug,
                        $category->description,
                        $category->status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV Template for Import.
     */
    public function template()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // Write only headers
            fputcsv($file, ['ID (Optional)', 'Name', 'Type (product/service)', 'Slug (Optional)', 'Description (Optional)', 'Status (active/inactive)']);

            // Add a sample row to help the user
            fputcsv($file, ['', 'Sample Category', 'product', 'sample-category', 'This is a sample category', 'active']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import categories from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        // Open the file
        if (($handle = fopen($path, 'r')) !== false) {
            // Get the header
            $header = fgetcsv($handle);

            $importedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                // Assuming standard columns: [0=>ID, 1=>Name, 2=>Type, 3=>Slug, 4=>Description, 5=>Status]
                if (count($row) >= 6 && ! empty(trim($row[1]))) {
                    $name = trim($row[1]);
                    $type = trim(strtolower($row[2]));
                    $type = in_array($type, ['product', 'service']) ? $type : 'product';
                    $slug = empty(trim($row[3])) ? Str::slug($name) : trim($row[3]);
                    $description = trim($row[4]);
                    $status = trim(strtolower($row[5]));
                    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';

                    Category::updateOrCreate(
                        ['name' => $name, 'type' => $type],
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
            'message' => "Successfully imported $importedCount categories.",
            'alert-type' => 'success',
        ]);
    }
}

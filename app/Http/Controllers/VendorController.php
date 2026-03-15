<?php

namespace App\Http\Controllers;

use App\Models\PurchaseBill;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Vendor::query();

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('vendor_id', 'like', "%{$search}%");
            });
        }

        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        if ($request->type && $request->type !== 'All Types') {
            $query->where('type', strtolower($request->type));
        }

        $vendors = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $stats = [
            'total' => Vendor::count(),
            'active' => Vendor::where('status', 'active')->count(),
            'total_payable' => Vendor::sum('balance'),
            'total_purchases' => Vendor::sum('total_purchases'),
        ];

        return view('admin.contacts.vendors', compact('vendors', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'type' => 'required|in:individual,company',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:net_30,net_15,due_on_receipt,net_60',
            'credit_limit' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['vendor_id'] = 'VEN-'.str_pad(Vendor::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['balance'] = $validated['balance'] ?? 0;

        Vendor::create($validated);

        return redirect()->route('contacts.vendors.index')
            ->with('message', 'Vendor created successfully.')
            ->with('title', 'Vendor Created')
            ->with('alert-type', 'success');
    }

    public function show($id): JsonResponse
    {
        $vendor = Vendor::findOrFail($id);

        return response()->json($vendor);
    }

    public function details($id): View
    {
        $vendor = Vendor::with(['purchaseOrders' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(5);
        }, 'vendorPayments' => function ($q) {
            $q->orderBy('created_at', 'desc')->take(5);
        }])->findOrFail($id);

        $vendor->total_purchases_val = PurchaseBill::where('vendor_id', $vendor->id)->sum('grand_total');
        $vendor->total_payable_val = PurchaseBill::where('vendor_id', $vendor->id)->sum('balance_amount');
        $vendor->pending_orders_val = PurchaseOrder::where('vendor_id', $vendor->id)->where('status', 'pending')->sum('grand_total');

        return view('admin.contacts.vendor_details', compact('vendor'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $vendor = Vendor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'type' => 'required|in:individual,company',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:net_30,net_15,due_on_receipt,net_60',
            'credit_limit' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['balance'] = $validated['balance'] ?? 0;
        $vendor->update($validated);

        return redirect()->route('contacts.vendors.index')
            ->with('message', 'Vendor updated successfully.')
            ->with('title', 'Vendor Updated')
            ->with('alert-type', 'success');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->delete();

            return redirect()->route('contacts.vendors.index')
                ->with('message', 'Vendor deleted successfully.')
                ->with('title', 'Vendor Deleted')
                ->with('alert-type', 'success');
        } catch (\Exception $e) {
            return redirect()->route('contacts.vendors.index')
                ->with('message', 'Cannot delete vendor. They may have active procurement records or related data.')
                ->with('title', 'Delete Failed')
                ->with('alert-type', 'error');
        }
    }

    /**
     * Download CSV Template for Import.
     */
    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendors_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Name',
                'Email',
                'Phone',
                'Tax ID',
                'Type (distributor/manufacturer/wholesaler)',
                'Address',
                'City',
                'Country',
                'Payment Terms',
                'Credit Limit',
                'Status (active/inactive)',
            ]);

            fputcsv($file, [
                'Acme Corp',
                'contact@acme.com',
                '+1987654321',
                'TAX-12345',
                'manufacturer',
                '456 Industrial Way',
                'Chicago',
                'USA',
                'Net 60',
                '20000',
                'active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export vendors to CSV.
     */
    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="vendors.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Vendor ID',
                'Name',
                'Email',
                'Phone',
                'Tax ID',
                'Type',
                'Address',
                'City',
                'Country',
                'Payment Terms',
                'Credit Limit',
                'Balance',
                'Total Purchases',
                'Status',
            ]);

            Vendor::chunk(100, function ($vendors) use ($file) {
                foreach ($vendors as $vendor) {
                    fputcsv($file, [
                        $vendor->id,
                        $vendor->vendor_id,
                        $vendor->name,
                        $vendor->email,
                        $vendor->phone,
                        $vendor->tax_id,
                        $vendor->type,
                        $vendor->address,
                        $vendor->city,
                        $vendor->country,
                        $vendor->payment_terms,
                        $vendor->credit_limit,
                        $vendor->balance,
                        $vendor->total_purchases,
                        $vendor->status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import vendors from CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle);
            $importedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 11 && ! empty(trim($row[0])) && ! empty(trim($row[1]))) {
                    $name = trim($row[0]);
                    $email = trim($row[1]);
                    $phone = trim($row[2]);
                    $taxId = trim($row[3]);
                    $type = strtolower(trim($row[4]));
                    $type = in_array($type, ['distributor', 'manufacturer', 'wholesaler']) ? $type : 'distributor';
                    $address = trim($row[5]);
                    $city = trim($row[6]);
                    $country = trim($row[7]);
                    $paymentTerms = trim($row[8]);
                    $creditLimit = ! empty(trim($row[9])) ? trim($row[9]) : 0;
                    $status = strtolower(trim($row[10]));
                    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';

                    $existing = Vendor::where('email', $email)->first();
                    
                    Vendor::updateOrCreate(
                        ['email' => $email],
                        [
                            'vendor_id' => optional($existing)->vendor_id ?? 'VND-'.strtoupper(uniqid()),
                            'name' => $name,
                            'phone' => $phone,
                            'tax_id' => $taxId,
                            'type' => $type,
                            'address' => $address,
                            'city' => $city,
                            'country' => $country,
                            'payment_terms' => $paymentTerms,
                            'credit_limit' => is_numeric($creditLimit) ? $creditLimit : 0,
                            'status' => $status,
                        ]
                    );

                    $importedCount++;
                }
            }

            fclose($handle);
        }

        return redirect()->route('contacts.vendors.index')
            ->with('message', "Successfully imported $importedCount vendors.")
            ->with('title', 'Import Complete')
            ->with('alert-type', 'success');
    }
}

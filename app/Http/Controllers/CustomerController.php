<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('customer_id', 'like', "%{$search}%");
            });
        }

        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        if ($request->type && $request->type !== 'All Types') {
            $query->where('type', strtolower($request->type));
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'total_receivable' => Customer::sum('balance'),
            'total_sales' => Customer::sum('total_sales'),
        ];

        return view('admin.contacts.customers', compact('customers', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
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

        $validated['customer_id'] = 'CUS-'.str_pad(Customer::count() + 1, 4, '0', STR_PAD_LEFT);
        $validated['credit_limit'] = $validated['credit_limit'] ?? 0;
        $validated['balance'] = $validated['balance'] ?? 0;

        Customer::create($validated);

        return redirect()->route('contacts.customers.index')
            ->with('message', 'Customer created successfully.')
            ->with('title', 'Customer Created')
            ->with('alert-type', 'success');
    }

    public function show($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);

        return response()->json($customer);
    }

    public function details($id): View
    {
        $customer = Customer::with([
            'invoices' => fn($q) => $q->latest()->limit(5),
            'payments' => fn($q) => $q->latest()->limit(5)
        ])->findOrFail($id);

        return view('admin.contacts.customer_details', compact('customer'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
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
        $customer->update($validated);

        return redirect()->route('contacts.customers.index')
            ->with('message', 'Customer updated successfully.')
            ->with('title', 'Customer Updated')
            ->with('alert-type', 'success');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return redirect()->route('contacts.customers.index')
                ->with('message', 'Customer deleted successfully.')
                ->with('title', 'Customer Deleted')
                ->with('alert-type', 'success');
        } catch (\Exception $e) {
            return redirect()->route('contacts.customers.index')
                ->with('message', 'Cannot delete customer. They may have active transactions or related records.')
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
            'Content-Disposition' => 'attachment; filename="customers_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Name',
                'Email',
                'Phone',
                'Type (individual/business)',
                'Address',
                'City',
                'Country',
                'Payment Terms',
                'Credit Limit',
                'Status (active/inactive)',
            ]);

            fputcsv($file, [
                'John Doe',
                'john@example.com',
                '+123456789',
                'individual',
                '123 Main St',
                'New York',
                'USA',
                'Net 30',
                '5000',
                'active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export customers to CSV.
     */
    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Customer ID',
                'Name',
                'Email',
                'Phone',
                'Type',
                'Address',
                'City',
                'Country',
                'Payment Terms',
                'Credit Limit',
                'Balance',
                'Total Sales',
                'Status',
            ]);

            Customer::chunk(100, function ($customers) use ($file) {
                foreach ($customers as $customer) {
                    fputcsv($file, [
                        $customer->id,
                        $customer->customer_id,
                        $customer->name,
                        $customer->email,
                        $customer->phone,
                        $customer->type,
                        $customer->address,
                        $customer->city,
                        $customer->country,
                        $customer->payment_terms,
                        $customer->credit_limit,
                        $customer->balance,
                        $customer->total_sales,
                        $customer->status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import customers from CSV.
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
                if (count($row) >= 10 && ! empty(trim($row[0])) && ! empty(trim($row[1]))) {
                    $name = trim($row[0]);
                    $email = trim($row[1]);
                    $phone = trim($row[2]);
                    $type = strtolower(trim($row[3]));
                    $type = in_array($type, ['individual', 'business']) ? $type : 'individual';
                    $address = trim($row[4]);
                    $city = trim($row[5]);
                    $country = trim($row[6]);
                    $paymentTerms = trim($row[7]);
                    $creditLimit = ! empty(trim($row[8])) ? trim($row[8]) : 0;
                    $status = strtolower(trim($row[9]));
                    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';

                    $existing = Customer::where('email', $email)->first();
                    
                    Customer::updateOrCreate(
                        ['email' => $email],
                        [
                            'customer_id' => optional($existing)->customer_id ?? 'CUS-'.strtoupper(uniqid()),
                            'name' => $name,
                            'phone' => $phone,
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

        return redirect()->route('contacts.customers.index')
            ->with('message', "Successfully imported $importedCount customers.")
            ->with('title', 'Import Complete')
            ->with('alert-type', 'success');
    }
}

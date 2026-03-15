<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanySettingController extends Controller
{
    /**
     * Show the company settings form.
     */
    public function index(): View
    {
        $settings = CompanySetting::first() ?? new CompanySetting;
        $branches = Branch::where('status', 'active')->orderBy('name')->get();
        $stores = Store::where('status', 'active')->orderBy('name')->get();

        return view('admin.settings.company', compact('settings', 'branches', 'stores'));
    }

    /**
     * Save company settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_legal_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'phone_whatsapp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'company_type' => 'nullable|string|max:50',
            'industry' => 'nullable|string|max:100',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_country' => 'nullable|string|max:100',
            'default_currency' => 'required|string|max:50',
            'secondary_currency' => 'nullable|string|max:3',
            'accounting_method' => 'nullable|in:cash,accrual',
            'rounding_method' => 'nullable|in:per_line,per_total',
            'fiscal_year_start' => 'required|string',
            'books_lock_date' => 'nullable|date',
            'payment_terms_days' => 'nullable|integer|min:0',
            'vendor_payment_terms' => 'nullable|integer|min:0',
            'invoice_prefix' => 'nullable|string|max:20',
            'next_invoice_number' => 'nullable|string|max:50',
            'po_prefix' => 'nullable|string|max:20',
            'next_po_number' => 'nullable|string|max:50',
            'invoice_template' => 'nullable|string|max:50',
            'invoice_footer_note' => 'nullable|string',
            'default_purchase_branch_id' => 'nullable|integer|exists:branches,id',
            'default_purchase_store_id' => 'nullable|integer|exists:stores,id',
            'default_sales_branch_id' => 'nullable|integer|exists:branches,id',
            'default_sales_store_id' => 'nullable|integer|exists:stores,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'costing_method' => 'nullable|string|in:FIFO,Average,LIFO',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'due_reminder_days' => 'nullable|integer|min:0',
            'payment_bank_details' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'date_format' => 'nullable|string|max:20',
            'decimal_precision' => 'nullable|integer|min:0|max:4',
        ]);

        $settings = CompanySetting::first() ?? new CompanySetting;

        // Handle logo upload — saved to public/uploads/company/ (no symlink needed)
        if ($request->hasFile('logo')) {
            // Delete old logo file if it exists
            if ($settings->logo) {
                $oldPath = public_path($settings->logo);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('logo');
            $filename = Str::random(40).'.'.$file->getClientOriginalExtension();
            $dir = public_path('uploads/company');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $filename);
            $settings->logo = 'uploads/company/'.$filename;
        }

        // Boolean toggles
        $settings->multi_currency_enabled = $request->has('multi_currency_enabled');
        $settings->inventory_tracking_enabled = $request->has('inventory_tracking_enabled');
        $settings->auto_invoice_reminders = $request->has('auto_invoice_reminders');
        $settings->enable_discount = $request->has('enable_discount');

        // Fill all other fields
        $settings->fill($request->except([
            'logo',
            'multi_currency_enabled',
            'inventory_tracking_enabled',
            'auto_invoice_reminders',
            'enable_discount',
            '_token',
            '_method',
        ]));
        $settings->save();

        return redirect()->route('settings.company')
            ->with([
                'message' => 'Company settings saved successfully!',
                'title' => 'Settings Updated',
                'alert-type' => 'success',
            ]);
    }
}

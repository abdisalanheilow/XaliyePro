@extends('admin.admin_master')

@section('title', 'Company Settings - XaliyePro')

@section('admin')

    {{-- Page Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Company Settings</h1>
            <p class="text-gray-500 mt-1 text-sm">Manage your company information and system preferences</p>
        </div>
        <div
            class="flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm">
            <i data-lucide="clock" class="w-4 h-4 text-[#28A375]"></i>
            Last saved: {{ $settings->updated_at ? $settings->updated_at->diffForHumans() : 'Never' }}
        </div>
    </div>

    @if (session('success'))
        <div
            class="mb-6 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-5 py-4 flex items-center gap-3 shadow-sm">
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
            </div>
            <div>
                <p class="font-semibold">Settings Saved</p>
                <p class="text-green-600">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @php
        $logoUrl = ($settings->logo && file_exists(public_path($settings->logo)))
            ? asset($settings->logo) : '';
        $xCompanyName = old('company_name', $settings->company_name ?? '');
        $xEmail = old('email', $settings->email ?? '');
        $xPhone = old('phone', $settings->phone ?? '');
        $xWhatsapp = old('phone_whatsapp', $settings->phone_whatsapp ?? '');
        $xWebsite = old('website', $settings->website ?? '');
        $xCompanyType = old('company_type', $settings->company_type ?? '');
        $xStreet = old('street_address', $settings->street_address ?? '');
        $xCity = old('city', $settings->city ?? '');
        $xCountry = old('country', $settings->country ?? '');
        $xCurrency = old('default_currency', $settings->default_currency ?? 'USD - US Dollar');
        $xSecCurrency = old('secondary_currency', $settings->secondary_currency ?? '');
        $xFiscalYear = old('fiscal_year_start', $settings->fiscal_year_start ?? '');
        $xInvPrefix = old('invoice_prefix', $settings->invoice_prefix ?? 'INV');
        $xNextInv = old('next_invoice_number', $settings->next_invoice_number ?? '');
        $xPayTerms = old('payment_terms_days', $settings->payment_terms_days ?? 30);
        $xBankDetails = old('payment_bank_details', $settings->payment_bank_details ?? '');
    @endphp

    <form method="POST" action="{{ route('settings.company.update') }}" enctype="multipart/form-data" class="space-y-6"
        x-data='{
                                        preview: false,
                                        logoSrc:       {!! json_encode($logoUrl) !!},
                                        name:          {!! json_encode($xCompanyName) !!},
                                        email:         {!! json_encode($xEmail) !!},
                                        phone:         {!! json_encode($xPhone) !!},
                                        whatsapp:      {!! json_encode($xWhatsapp) !!},
                                        website:       {!! json_encode($xWebsite) !!},
                                        company_type:  {!! json_encode($xCompanyType) !!},
                                        street:        {!! json_encode($xStreet) !!},
                                        city:          {!! json_encode($xCity) !!},
                                        country:       {!! json_encode($xCountry) !!},
                                        currency:      {!! json_encode($xCurrency) !!},
                                        sec_currency:  {!! json_encode($xSecCurrency) !!},
                                        fiscal_year:   {!! json_encode($xFiscalYear) !!},
                                        inv_prefix:    {!! json_encode($xInvPrefix) !!},
                                        next_inv:      {!! json_encode($xNextInv) !!},
                                        pay_terms:     {!! json_encode((string) $xPayTerms) !!},
                                        bank_details:  {!! json_encode($xBankDetails) !!},
                                        initials() {
                                            return this.name
                                                ? this.name.split(" ").map(w => w[0]).join("").substring(0,2).toUpperCase()
                                                : "--";
                                        },
                                        handleLogo(e) {
                                            const file = e.target.files[0];
                                            if (file) { const r = new FileReader(); r.onload = ev => this.logoSrc = ev.target.result; r.readAsDataURL(file); }
                                        }
                                    }'>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT COLUMN (2/3) --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- �  Business Identity --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div
                            class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-[#28A375]/5 to-transparent">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-[#28A375]/10 rounded-xl flex items-center justify-center">
                                    <i data-lucide="building-2" class="w-5 h-5 text-[#28A375]"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">Business Identity</h3>
                                    <p class="text-xs text-gray-500">Names, contact details, and registration</p>
                                </div>
                            </div>
                            <span
                                class="text-[10px] font-semibold px-2 py-1 bg-[#28A375]/10 text-[#28A375] rounded-full uppercase tracking-wide">Required</span>
                        </div>
                        <div class="p-6 space-y-6">

                            {{-- Sub-group: Names --}}
                            <div>
                                <p
                                    class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span class="w-4 h-px bg-gray-300 inline-block"></span> Company Names
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Trading / Display Name
                                            <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i data-lucide="award" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="company_name" x-model="name"
                                                value="{{ old('company_name', $settings->company_name) }}"
                                                placeholder="e.g. Mustaqbal Trading Co."
                                                class="w-full pl-9 pr-4 py-2.5 border {{ $errors->has('company_name') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all"
                                                required>
                                        </div>
                                        @error('company_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Legal / Registered
                                            Name</label>
                                        <div class="relative">
                                            <i data-lucide="scale" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="company_legal_name"
                                                value="{{ old('company_legal_name', $settings->company_legal_name) }}"
                                                placeholder="Official name on government records"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                        <p class="text-xs text-gray-400 mt-1">Leave blank if same as trading name</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Type</label>
                                        <div class="relative">
                                            <i data-lucide="briefcase" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="company_type" class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="">Select Type</option>
                                                @foreach (['LLC', 'Sole Proprietor', 'Partnership', 'Corporation', 'NGO', 'Co-operative'] as $type)
                                                    <option value="{{ $type }}" @selected(old('company_type', $settings->company_type) == $type)>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Industry / Sector</label>
                                        <div class="relative">
                                            <i data-lucide="tag" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="industry" class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="">Select industry</option>
                                                @foreach (['Retail & Wholesale', 'Import & Export', 'Construction', 'Manufacturing', 'Food & Beverage', 'Telecommunications', 'Financial Services', 'Healthcare', 'Education', 'Logistics & Transport', 'Agriculture', 'Technology', 'Hospitality', 'Real Estate', 'Other'] as $ind)
                                                    <option value="{{ $ind }}" @selected(old('industry', $settings->industry) == $ind)>{{ $ind }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sub-group: Contact --}}
                            <div class="pt-4 border-t border-dashed border-gray-100">
                                <p
                                    class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span class="w-4 h-px bg-gray-300 inline-block"></span> Contact & Communications
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Business Email <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i data-lucide="mail"
                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="email" name="email" x-model="email"
                                                value="{{ old('email', $settings->email) }}" placeholder="info@company.com"
                                                class="w-full pl-9 pr-4 py-2.5 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all"
                                                required>
                                        </div>
                                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i data-lucide="phone"
                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="tel" name="phone" x-model="phone"
                                                value="{{ old('phone', $settings->phone) }}" placeholder="+252 61 000 0000"
                                                class="w-full pl-9 pr-4 py-2.5 border {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all"
                                                required>
                                        </div>
                                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp Number</label>
                                        <div class="relative">
                                            <i data-lucide="message-circle"
                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="tel" name="phone_whatsapp" x-model="whatsapp"
                                                value="{{ old('phone_whatsapp', $settings->phone_whatsapp) }}"
                                                placeholder="+252 61 000 0000"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Website</label>
                                        <div class="relative">
                                            <i data-lucide="globe"
                                                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="website" x-model="website"
                                                value="{{ old('website', $settings->website) }}" placeholder="www.company.com"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Address Information --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50/60 to-transparent">
                                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                                    <i data-lucide="map-pin" class="w-5 h-5 text-blue-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">Address Information</h3>
                                    <p class="text-xs text-gray-500">Company address for invoices and shipping</p>
                                </div>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Street Address</label>
                                    <div class="relative">
                                        <i data-lucide="home"
                                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                        <input type="text" name="street_address" x-model="street"
                                            value="{{ old('street_address', $settings->street_address) }}"
                                            placeholder="123 Business Street"
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                                        <div class="relative">
                                            <i data-lucide="building" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="city" x-model="city" value="{{ old('city', $settings->city) }}"
                                                placeholder="Mogadishu"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Zip / Postal Code</label>
                                        <div class="relative">
                                            <i data-lucide="mail-search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="zip_code" value="{{ old('zip_code', $settings->zip_code) }}"
                                                placeholder="e.g. 10001"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Country</label>
                                        <div class="relative">
                                            <i data-lucide="globe-2" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="country" x-model="country"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="">Select country</option>
                                                @foreach (['Burundi', 'Comoros', 'Djibouti', 'Eritrea', 'Ethiopia', 'Kenya', 'Madagascar', 'Malawi', 'Mauritius', 'Mozambique', 'Rwanda', 'Seychelles', 'Somalia', 'South Sudan', 'Sudan', 'Tanzania', 'Uganda', 'Zambia', 'Zimbabwe'] as $c)
                                                    <option value="{{ $c }}" @selected(old('country', $settings->country) == $c)>{{ $c }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Financial Settings --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50/60 to-transparent">
                                <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center">
                                    <i data-lucide="landmark" class="w-5 h-5 text-yellow-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">Financial Settings</h3>
                                    <p class="text-xs text-gray-500">Currency, tax rate, and fiscal year</p>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Default Currency <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i data-lucide="coins" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="default_currency" class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all" required>
                                                @foreach (['USD - US Dollar', 'EUR - Euro', 'GBP - British Pound', 'CAD - Canadian Dollar', 'KES - Kenyan Shilling', 'TZS - Tanzanian Shilling', 'UGX - Ugandan Shilling', 'ETB - Ethiopian Birr', 'RWF - Rwandan Franc', 'SOS - Somali Shilling'] as $currency)
                                                    <option value="{{ $currency }}" @selected(old('default_currency', $settings->default_currency) == $currency)>{{ $currency }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Secondary Currency</label>
                                        <div class="relative">
                                            <i data-lucide="refresh-cw" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="secondary_currency" class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="">None</option>
                                                @foreach (['USD', 'EUR', 'GBP', 'CAD', 'KES', 'TZS', 'UGX', 'ETB', 'RWF', 'SOS'] as $currency)
                                                    <option value="{{ $currency }}" @selected(old('secondary_currency', $settings->secondary_currency) == $currency)>{{ $currency }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fiscal Year Start <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <i data-lucide="calendar-days" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="fiscal_year_start" class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all" required>
                                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                    <option value="{{ $month }}" @selected(old('fiscal_year_start', $settings->fiscal_year_start) == $month)>{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Invoice Settings --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50/60 to-transparent">
                                <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center">
                                    <i data-lucide="file-text" class="w-5 h-5 text-purple-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">Invoice Settings</h3>
                                    <p class="text-xs text-gray-500">Configure invoice numbering and templates</p>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice Prefix</label>
                                        <div class="relative">
                                            <i data-lucide="type" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="invoice_prefix"
                                                value="{{ old('invoice_prefix', $settings->invoice_prefix ?? 'INV') }}"
                                                placeholder="INV"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Next Invoice Number</label>
                                        <div class="relative">
                                            <i data-lucide="hash" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="text" name="next_invoice_number"
                                                value="{{ old('next_invoice_number', $settings->next_invoice_number ?? '2024001') }}"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Payment Terms (Days)</label>
                                        <div class="relative">
                                            <i data-lucide="calendar-clock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="number" name="payment_terms_days"
                                                value="{{ old('payment_terms_days', $settings->payment_terms_days ?? 30) }}"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Due Reminder (Days)</label>
                                        <div class="relative">
                                            <i data-lucide="bell-ring" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="number" name="due_reminder_days"
                                                value="{{ old('due_reminder_days', $settings->due_reminder_days ?? 3) }}"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice Template</label>
                                        <div class="relative">
                                            <i data-lucide="layout-template" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="invoice_template"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                @foreach (['Modern', 'Classic', 'Professional', 'Minimal'] as $template)
                                                    <option value="{{ $template }}" @selected(old('invoice_template', $settings->invoice_template) == $template)>{{ $template }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Bank & Mobile Money
                                            Details</label>
                                        <textarea name="payment_bank_details" rows="3"
                                            placeholder="e.g. EVC Plus: 061XXXXXXX, Dahabshiil USD: XXXX"
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all resize-none">{{ old('payment_bank_details', $settings->payment_bank_details) }}</textarea>
                                        <p class="text-xs text-gray-500 mt-1">These details will be printed on invoices.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Terms and Conditions</label>
                                        <textarea name="terms_and_conditions" rows="3" placeholder="Standard invoice terms..."
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all resize-none">{{ old('terms_and_conditions', $settings->terms_and_conditions) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Invoice Footer Note</label>
                                        <textarea name="invoice_footer_note" rows="2"
                                            placeholder="e.g. Thank you for your business! All payments due within 30 days."
                                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all resize-none">{{ old('invoice_footer_note', $settings->invoice_footer_note) }}</textarea>
                                        <p class="text-xs text-gray-400 mt-1">Printed at the very bottom of every invoice.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Default Locations --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                             x-data="{
                                purchBranchId: '{{ old('default_purchase_branch_id', $settings->default_purchase_branch_id) }}',
                                salesBranchId: '{{ old('default_sales_branch_id', $settings->default_sales_branch_id) }}',
                                allStores: {{ json_encode($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'branch_id' => $s->branch_id])) }},
                                purchSelStore: '{{ old('default_purchase_store_id', $settings->default_purchase_store_id) }}',
                                salesSelStore: '{{ old('default_sales_store_id', $settings->default_sales_store_id) }}',
                                get purchStores() { return this.allStores.filter(s => !this.purchBranchId || s.branch_id == this.purchBranchId); },
                                get salesStores() { return this.allStores.filter(s => !this.salesBranchId || s.branch_id == this.salesBranchId); },
                             }">
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50/60 to-transparent">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center">
                                        <i data-lucide="map-pin" class="w-5 h-5 text-indigo-500"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-sm">Default Locations</h3>
                                        <p class="text-xs text-gray-500">Pre-select branch &amp; store for purchases and sales</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-semibold px-2 py-1 bg-indigo-50 text-indigo-600 rounded-full uppercase tracking-wide">Optional</span>
                            </div>
                            <div class="p-6 space-y-6">

                                {{-- Purchase Location --}}
                                <div>
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5 text-cyan-500"></i>
                                        Default Purchase Location
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Branch</label>
                                            <div class="relative">
                                                <i data-lucide="building" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <select name="default_purchase_branch_id" x-model="purchBranchId"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                    <option value="">— No default —</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}" @selected(old('default_purchase_branch_id', $settings->default_purchase_branch_id) == $branch->id)>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Store / Warehouse</label>
                                            <div class="relative">
                                                <i data-lucide="warehouse" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <select name="default_purchase_store_id" x-model="purchSelStore"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                    <option value="">— No default —</option>
                                                    <template x-for="store in purchStores" :key="store.id">
                                                        <option :value="store.id" :selected="store.id == purchSelStore" x-text="store.name"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Sales Location --}}
                                <div class="pt-4 border-t border-dashed border-gray-100">
                                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <i data-lucide="receipt" class="w-3.5 h-3.5 text-emerald-500"></i>
                                        Default Sales Location
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Branch</label>
                                            <div class="relative">
                                                <i data-lucide="building" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <select name="default_sales_branch_id" x-model="salesBranchId"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                    <option value="">— No default —</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}" @selected(old('default_sales_branch_id', $settings->default_sales_branch_id) == $branch->id)>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Store / Outlet</label>
                                            <div class="relative">
                                                <i data-lucide="store" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <select name="default_sales_store_id" x-model="salesSelStore"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                    <option value="">— No default —</option>
                                                    <template x-for="store in salesStores" :key="store.id">
                                                        <option :value="store.id" :selected="store.id == salesSelStore" x-text="store.name"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN (1/3) --}}
                    <div class="space-y-6">

                        {{-- Logo Upload --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                                    <div class="w-9 h-9 bg-[#28A375]/10 rounded-lg flex items-center justify-center">
                                        <i data-lucide="image" class="w-5 h-5 text-[#28A375]"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-sm">Company Logo</h3>
                                        <p class="text-xs text-gray-500">For invoices & documents</p>
                                    </div>
                                </div>
                                <div class="p-6 flex flex-col items-center text-center">
                                    <div
                                        class="w-24 h-24 bg-gradient-to-br from-[#28A375] to-[#1a7a57] rounded-2xl flex items-center justify-center mb-4 shadow-md overflow-hidden">
                                        <template x-if="logoSrc">
                                            <img :src="logoSrc" alt="" class="w-full h-full object-cover" x-on:error="logoSrc = ''">
                                        </template>
                                        <template x-if="!logoSrc">
                                            <span class="text-white font-extrabold text-3xl" x-text="initials()"></span>
                                        </template>
                                    </div>
                                    <label
                                        class="flex items-center gap-2 px-4 py-2 border-2 border-dashed border-gray-300 rounded-xl text-sm font-medium text-gray-600 hover:border-[#28A375] hover:text-[#28A375] cursor-pointer transition-colors w-full justify-center">
                                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                        Upload Logo
                                        <input type="file" name="logo" class="hidden" accept="image/*" @change="handleLogo($event)">
                                    </label>
                                    <p class="text-xs text-gray-400 mt-2">PNG, JPG up to 2MB</p>
                                    @error('logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Additional Settings (Toggles) --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-orange-50/60 to-transparent">
                                    <div class="w-9 h-9 bg-orange-50 rounded-xl flex items-center justify-center">
                                        <i data-lucide="sliders-horizontal" class="w-5 h-5 text-orange-500"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-sm">Preferences</h3>
                                        <p class="text-xs text-gray-500">Feature toggles</p>
                                    </div>
                                </div>
                                <div class="p-6 space-y-5">

                                    {{-- Multi-Currency --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Multi-Currency</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Allow multiple currencies</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="multi_currency_enabled" class="sr-only peer"
                                                @checked(old('multi_currency_enabled', $settings->multi_currency_enabled ?? false))>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#28A375]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28A375]">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="border-t border-dashed border-gray-100"></div>

                                    {{-- Inventory Tracking --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Inventory Tracking</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Track stock movements</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="inventory_tracking_enabled" class="sr-only peer"
                                                @checked(old('inventory_tracking_enabled', $settings->inventory_tracking_enabled ?? true))>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#28A375]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28A375]">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="border-t border-dashed border-gray-100"></div>

                                    {{-- Auto Invoice Reminders --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Invoice Reminders</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Auto-send overdue reminders</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="auto_invoice_reminders" class="sr-only peer"
                                                @checked(old('auto_invoice_reminders', $settings->auto_invoice_reminders ?? true))>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#28A375]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28A375]">
                                            </div>
                                        </label>
                                    </div>

                                    <div class="border-t border-dashed border-gray-100"></div>

                                    {{-- Discount on Invoices --}}
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Discount on Invoices</p>
                                            <p class="text-xs text-gray-500 mt-0.5">Enable discount field</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                            <input type="checkbox" name="enable_discount" class="sr-only peer"
                                                @checked(old('enable_discount', $settings->enable_discount ?? true))>
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#28A375]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28A375]">
                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>
                            {{-- System Preferences --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50/60 to-transparent">
                                    <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center">
                                        <i data-lucide="settings-2" class="w-5 h-5 text-indigo-500"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-sm">System Preferences</h3>
                                        <p class="text-xs text-gray-500">Locale, timezone & display</p>
                                    </div>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">System Language</label>
                                        <div class="relative">
                                            <i data-lucide="languages" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="language"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="en" @selected(old('language', $settings->language ?? 'en') == 'en')>English</option>
                                                <option value="so" @selected(old('language', $settings->language) == 'so')>Somali (Af-Soomaali)</option>
                                                <option value="ar" @selected(old('language', $settings->language) == 'ar')>Arabic (عربي)</option>
                                                <option value="sw" @selected(old('language', $settings->language) == 'sw')>Swahili (Kiswahili)</option>
                                                <option value="fr" @selected(old('language', $settings->language) == 'fr')>French (Français)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Timezone</label>
                                        <div class="relative">
                                            <i data-lucide="globe" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="timezone"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                @foreach ([
                                                        'Africa/Mogadishu' => 'Mogadishu (EAT +3)',
                                                        'Africa/Nairobi' => 'Nairobi (EAT +3)',
                                                        'Africa/Dar_es_Salaam' => 'Dar es Salaam (EAT +3)',
                                                        'Africa/Kampala' => 'Kampala (EAT +3)',
                                                        'Africa/Addis_Ababa' => 'Addis Ababa (EAT +3)',
                                                        'Africa/Khartoum' => 'Khartoum (CAT +2)',
                                                        'UTC' => 'UTC +0',
                                                    ] as $tz => $label)
                                                                                <option value="{{ $tz }}" @selected(old('timezone', $settings->timezone ?? 'Africa/Mogadishu') == $tz)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Date Format</label>
                                        <div class="relative">
                                            <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="date_format"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="d/m/Y" @selected(old('date_format', $settings->date_format ?? 'd/m/Y') == 'd/m/Y')>DD/MM/YYYY</option>
                                                <option value="m/d/Y" @selected(old('date_format', $settings->date_format) == 'm/d/Y')>MM/DD/YYYY</option>
                                                <option value="Y-m-d" @selected(old('date_format', $settings->date_format) == 'Y-m-d')>YYYY-MM-DD</option>
                                                <option value="d-m-Y" @selected(old('date_format', $settings->date_format) == 'd-m-Y')>DD-MM-YYYY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Decimal Precision</label>
                                        <div class="relative">
                                            <i data-lucide="arrow-right-left" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="decimal_precision"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="0" @selected(old('decimal_precision', $settings->decimal_precision ?? 2) == 0)>0 (e.g. 1,500)</option>
                                                <option value="2" @selected(old('decimal_precision', $settings->decimal_precision ?? 2) == 2)>2 (e.g. 1,500.00)</option>
                                                <option value="3" @selected(old('decimal_precision', $settings->decimal_precision ?? 2) == 3)>3 (e.g. 1,500.000)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        

                        {{-- Inventory Settings --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50/60 to-transparent">
                                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                                    <i data-lucide="package" class="w-5 h-5 text-blue-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-sm">Inventory Settings</h3>
                                    <p class="text-xs text-gray-500">Manage costing methods and stock thresholds</p>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Costing Method</label>
                                        <div class="relative">
                                            <i data-lucide="calculator" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <select name="costing_method"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                <option value="FIFO" @selected(old('costing_method', $settings->costing_method) == 'FIFO')>FIFO (First In, First Out)</option>
                                                <option value="Average" @selected(old('costing_method', $settings->costing_method) == 'Average')>Average Cost</option>
                                                <option value="LIFO" @selected(old('costing_method', $settings->costing_method) == 'LIFO')>LIFO (Last In, First Out)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Low Stock Alert Threshold</label>
                                        <div class="relative">
                                            <i data-lucide="alert-triangle" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                            <input type="number" name="low_stock_threshold"
                                                value="{{ old('low_stock_threshold', $settings->low_stock_threshold ?? 5) }}"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            {{-- Save Card --}}
                            <div class="bg-gradient-to-br from-[#28A375] to-[#1a7a57] rounded-2xl shadow-md p-6 text-white">
                                <div class="flex items-center gap-2 mb-1">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                    <h3 class="font-semibold text-sm">Save Changes</h3>
                                </div>
                                <p class="text-green-100 text-xs mb-5">All changes will take effect immediately across the system.</p>

                                {{-- Preview Button --}}
                                <button type="button" @click="preview = true"
                                    class="w-full bg-white/20 hover:bg-white/30 border border-white/30 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition-colors flex items-center justify-center gap-2 mb-3">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Preview Before Saving
                                </button>

                                <button type="submit"
                                    class="w-full bg-white text-[#28A375] font-bold py-2.5 px-4 rounded-xl text-sm hover:bg-green-50 transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Save Settings
                                </button>
                                <a href="{{ route('dashboard') }}"
                                    class="mt-3 w-full text-green-200 hover:text-white text-xs text-center block transition-colors">
                                    Cancel and go back
                                </a>
                            </div>

                        </div>
                    </div>

                    {{-- Preview Modal --}}
                    <div x-show="preview" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                        @click.self="preview = false" x-cloak>
                        <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col" style="max-height:90vh">

                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gray-50 shrink-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-[#28A375]/10 rounded-xl flex items-center justify-center">
                                        <i data-lucide="eye" class="w-5 h-5 text-[#28A375]"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900">Company Preview</h3>
                                        <p class="text-xs text-gray-500">Review all settings before saving</p>
                                    </div>
                                </div>
                                <button type="button" @click="preview = false"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200 text-gray-500 transition-colors">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">

                                {{-- Hero Banner --}}
                                <div class="bg-gradient-to-r from-[#2B352F] to-[#1a201d] rounded-2xl p-6 mb-6 flex items-center gap-5">
                                    <div class="w-20 h-20 bg-gradient-to-br from-[#28A375] to-[#1a7a57] rounded-2xl flex items-center justify-center overflow-hidden shrink-0 shadow-lg border-2 border-white/20">
                                        <template x-if="logoSrc">
                                            <img :src="logoSrc" alt="" class="w-full h-full object-cover" x-on:error="logoSrc = ''">
                                        </template>
                                        <template x-if="!logoSrc">
                                            <span class="text-white font-extrabold text-3xl" x-text="initials()"></span>
                                        </template>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h2 class="text-white font-bold text-xl leading-tight" x-text="name || 'Company Name'"></h2>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
                                            <p class="text-green-300 text-sm flex items-center gap-1">
                                                <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                                <span x-text="email || '—'"></span>
                                            </p>
                                            <p class="text-gray-300 text-sm flex items-center gap-1">
                                                <i data-lucide="phone" class="w-3.5 h-3.5"></i>
                                                <span x-text="phone || '—'"></span>
                                            </p>
                                            <p class="text-blue-300 text-sm flex items-center gap-1" x-show="whatsapp">
                                                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                                                <span x-text="whatsapp"></span>
                                            </p>
                                            <p class="text-gray-400 text-sm flex items-center gap-1" x-show="website">
                                                <i data-lucide="globe" class="w-3.5 h-3.5"></i>
                                                <span x-text="website"></span>
                                            </p>
                                        </div>
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <span x-show="company_type" class="px-2 py-0.5 bg-white/10 text-white/80 text-xs rounded-full" x-text="company_type"></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Two-column detail grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- LEFT SIDE --}}
                                    <div class="space-y-4">

                                        {{-- Location --}}
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-400"></i> Location
                                            </p>
                                            <p class="text-sm text-gray-800 font-medium" x-show="street" x-text="street"></p>
                                            <p class="text-sm text-gray-800 font-medium">
                                                <span x-text="city || ''"></span><span x-show="city && country">, </span><span x-text="country || ''"></span>
                                                <span x-show="!city && !country && !street" class="text-gray-400">Not specified</span>
                                            </p>
                                        </div>

                                        {{-- Financial --}}
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                                <i data-lucide="landmark" class="w-3.5 h-3.5 text-yellow-500"></i> Financial
                                            </p>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">Base Currency</p>
                                                    <p class="text-sm font-semibold text-gray-800" x-text="currency || '—'"></p>
                                                </div>
                                                <div x-show="sec_currency">
                                                    <p class="text-xs text-gray-400 mb-0.5">Secondary Currency</p>
                                                    <p class="text-sm font-semibold text-gray-800" x-text="sec_currency"></p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">Fiscal Year</p>
                                                    <p class="text-sm font-semibold text-gray-800" x-text="fiscal_year || '—'"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- RIGHT SIDE --}}
                                    <div class="space-y-4">

                                        {{-- Invoice Config --}}
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-3 flex items-center gap-1.5">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-purple-500"></i> Invoice Config
                                            </p>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">Prefix</p>
                                                    <p class="text-sm font-semibold text-gray-800" x-text="inv_prefix || 'INV'"></p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">Next Number</p>
                                                    <p class="text-sm font-semibold text-gray-800" x-text="next_inv || '—'"></p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">Payment Terms</p>
                                                    <p class="text-sm font-semibold text-gray-800"><span x-text="pay_terms"></span> days</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Bank / Mobile Money --}}
                                        <div class="bg-gray-50 rounded-xl p-4" x-show="bank_details">
                                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2 flex items-center gap-1.5">
                                                <i data-lucide="credit-card" class="w-3.5 h-3.5 text-green-500"></i> Payment Details
                                            </p>
                                            <p class="text-sm text-gray-700 whitespace-pre-line" x-text="bank_details"></p>
                                        </div>

                                        {{-- Notice --}}
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2">
                                            <i data-lucide="info" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                                            <p class="text-xs text-amber-700">This preview reflects your entered data. Click
                                                <strong>Confirm &amp; Save</strong> to apply.
                                            </p>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            {{-- Modal Footer --}}
                            <div class="flex gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 shrink-0">
                                <button type="button" @click="preview = false"
                                    class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                                    ← Go Back &amp; Edit
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-[#28A375] hover:bg-[#229967] text-white font-bold py-2.5 px-4 rounded-xl text-sm transition-colors flex items-center justify-center gap-2 shadow-sm">
                                    <i data-lucide="save" class="w-4 h-4"></i>
                                    Confirm &amp; Save
                                </button>
                            </div>

                        </div>
                    </div>

                </form>

@endsection

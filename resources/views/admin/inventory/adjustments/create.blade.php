@extends('admin.admin_master')

@section('title', 'New Stock Adjustment - XaliyePro')

@section('admin')
<div class="space-y-6" x-data="adjustmentForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Stock Adjustment</h1>
            <p class="text-sm text-gray-500">Record manual inventory corrections for a specific location</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.adjustments.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                Cancel
            </a>
            <button @click="submitForm" class="px-6 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all shadow-sm active:scale-95">
                Save Adjustment
            </button>
        </div>
    </div>

    <form id="adjustment-form" action="{{ route('inventory.adjustments.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Item Selection Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Adjustment Items</h3>
                        <div class="relative w-64" x-data="{ open: false, search: '', results: [] }">
                            <input 
                                type="text" 
                                x-model="search"
                                @input.debounce.300ms="
                                    if(search.length > 1) {
                                        fetch(`{{ route('items.search') }}?q=${search}`)
                                            .then(res => res.json())
                                            .then(data => { results = data; open = true; })
                                    } else { open = false; }
                                "
                                placeholder="Search item by name/SKU..."
                                class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all"
                            >
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                            
                            <!-- Search Results Dropdown -->
                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden max-h-64 overflow-y-auto"
                            >
                                <template x-for="item in results" :key="item.id">
                                    <div 
                                        @click="addItem(item); open = false; search = ''"
                                        class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between border-b border-gray-50 last:border-0"
                                    >
                                        <div>
                                            <div class="text-xs font-bold text-gray-900" x-text="item.name"></div>
                                            <div class="text-[10px] text-gray-400" x-text="'SKU: ' + item.sku"></div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-[10px] font-bold text-gray-500" x-text="item.current_stock + ' ' + (item.unit?.short_name || 'Units')"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/30">
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Item Details</th>
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">On Hand</th>
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right w-32">New Quantity</th>
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Difference</th>
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(line, index) in lines" :key="index">
                                    <tr class="hover:bg-gray-50/50 transition-all">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded bg-gray-50 flex items-center justify-center border border-gray-100">
                                                    <i data-lucide="package" class="w-4 h-4 text-gray-400"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900" x-text="line.name"></div>
                                                    <div class="text-[10px] text-gray-400 font-bold" x-text="'SKU: ' + line.sku"></div>
                                                    <input type="hidden" :name="`items[${index}][item_id]`" :value="line.item_id">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-bold text-gray-600" x-text="line.current_stock"></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input 
                                                type="number" 
                                                :name="`items[${index}][quantity]`" 
                                                x-model="line.new_quantity"
                                                class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-right focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all"
                                                step="any"
                                            >
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-black" :class="(line.new_quantity - line.current_stock) >= 0 ? 'text-green-600' : 'text-red-600'">
                                                <span x-text="(line.new_quantity - line.current_stock) >= 0 ? '+' : ''"></span>
                                                <span x-text="(line.new_quantity - line.current_stock).toFixed(2)"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center">
                                                <button @click.prevent="removeItem(index)" class="p-1.5 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-lg transition-all">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="lines.length === 0">
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center opacity-40">
                                            <div class="p-4 bg-gray-100 rounded-full mb-3">
                                                <i data-lucide="layers" class="w-8 h-8"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-500">No items added to adjustment</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <label class="block text-sm font-bold text-gray-900 mb-2">Additional Notes</p>
                    <textarea 
                        name="notes" 
                        rows="4" 
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all"
                        placeholder="Explain why this adjustment is being made..."
                    ></textarea>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-50">General Info</h3>
                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Adjustment Number</label>
                        <input 
                            type="text" 
                            name="adjustment_no" 
                            value="{{ $nextNum }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#28A375] outline-none"
                            readonly
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Date</label>
                        <input 
                            type="date" 
                            name="adjustment_date" 
                            value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#28A375] outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Store / Location</label>
                        <select 
                            name="store_id" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#28A375] outline-none"
                        >
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Adjustment Reason</label>
                        <select 
                            name="reason" 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#28A375] outline-none"
                        >
                            <option value="Stock Reconciliation">Stock Reconciliation</option>
                            <option value="Damaged Items">Damaged Items</option>
                            <option value="Missing / Theft">Missing / Theft</option>
                            <option value="Error during GRN">Error during GRN</option>
                            <option value="System Sync">System Sync</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="bg-[#28A375] rounded-xl shadow-lg shadow-green-100 p-6 text-white space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider opacity-80">Adjustment Summary</h3>
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <span class="text-xs opacity-80">Total Lines</span>
                        <span class="text-lg font-black" x-text="lines.length"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs opacity-80">Net Impact</span>
                        <span class="text-lg font-black" x-text="calculateNetImpact()"></span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function adjustmentForm() {
        return {
            lines: [],
            
            addItem(item) {
                // Check if already exists
                if(this.lines.find(l => l.item_id === item.id)) {
                    alert('Item already added to list');
                    return;
                }
                
                this.lines.push({
                    item_id: item.id,
                    name: item.name,
                    sku: item.sku,
                    current_stock: item.current_stock,
                    new_quantity: item.current_stock
                });

                this.$nextTick(() => {
                    lucide.createIcons();
                });
            },

            removeItem(index) {
                this.lines.splice(index, 1);
            },

            calculateNetImpact() {
                let net = this.lines.reduce((acc, line) => {
                    return acc + (parseFloat(line.new_quantity) - parseFloat(line.current_stock));
                }, 0);
                return (net >= 0 ? '+' : '') + net.toFixed(2);
            },

            submitForm() {
                if(this.lines.length === 0) {
                    alert('Please add at least one item');
                    return;
                }
                document.getElementById('adjustment-form').submit();
            }
        }
    }
</script>
@endsection

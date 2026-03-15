@extends('admin.admin_master')

@section('title', 'New Stock Transfer - XaliyePro')

@section('admin')
<div class="space-y-6" x-data="transferForm()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Stock Transfer</h1>
            <p class="text-sm text-gray-500">Move inventory between two store locations</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.transfers.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                Cancel
            </a>
            <button @click="submitForm" class="px-6 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all shadow-sm active:scale-95">
                Initiate Transfer
            </button>
        </div>
    </div>

    <form id="transfer-form" action="{{ route('inventory.transfers.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Location Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center relative">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Source Location (From)</label>
                            <select 
                                name="from_store_id" 
                                x-model="from_store_id"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#28A375] outline-none transition-all"
                            >
                                <option value="">Select Source Store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Arrow Icon -->
                        <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 -mt-1 w-10 h-10 bg-white border border-gray-100 rounded-full items-center justify-center shadow-sm z-10">
                            <i data-lucide="arrow-right" class="w-5 h-5 text-[#28A375]"></i>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Destination Location (To)</label>
                            <select 
                                name="to_store_id" 
                                x-model="to_store_id"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-[#28A375] outline-none transition-all"
                            >
                                <option value="">Select Destination Store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div x-show="from_store_id && to_store_id && from_store_id == to_store_id" class="mt-3 p-3 bg-red-50 text-red-600 rounded-lg text-xs font-bold flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        Source and Destination locations must be different.
                    </div>
                </div>

                <!-- Items Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Transfer Items</h3>
                        <div class="relative w-64" x-data="{ open: false, search: '', results: [] }">
                            <input 
                                type="text" 
                                x-model="search"
                                @input.debounce.300ms="
                                    if(search.length > 1) {
                                        fetch(`{{ route('items.search') }}?q=${search}`)
                                            .then(res => res.json())
                                            .then(data => { results = data; open = true; })
                                            .catch(err => { console.error('Item search failed:', err); results = []; open = false; })
                                    } else { open = false; }
                                "
                                placeholder="Search item to move..."
                                class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-[#28A375] outline-none"
                            >
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                            
                            <div x-show="open" @click.away="open = false" class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden max-h-64 overflow-y-auto">
                                <template x-for="item in results" :key="item.id">
                                    <div @click="addItem(item); open = false; search = ''" class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between border-b border-gray-50">
                                        <div>
                                            <div class="text-xs font-bold text-gray-900" x-text="item.name"></div>
                                            <div class="text-[10px] text-gray-400" x-text="item.sku"></div>
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
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right w-32">Transfer Qty</th>
                                    <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="(line, index) in lines" :key="index">
                                    <tr class="hover:bg-gray-50/50 transition-all">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded bg-blue-50 flex items-center justify-center">
                                                    <i data-lucide="box" class="w-4 h-4 text-blue-500"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900" x-text="line.name"></div>
                                                    <div class="text-[10px] text-gray-400 font-bold" x-text="line.sku"></div>
                                                    <input type="hidden" :name="`items[${index}][item_id]`" :value="line.item_id">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input 
                                                type="number" 
                                                :name="`items[${index}][quantity]`" 
                                                x-model="line.quantity"
                                                class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-right focus:ring-2 focus:ring-[#28A375] outline-none"
                                                min="0.01" step="any"
                                            >
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-50">Transfer Info</h3>
                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer Number</label>
                        <input type="text" name="transfer_no" value="{{ $nextNum }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-900" readonly>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Date</label>
                        <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-900">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Internal Notes</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none" placeholder="Reference or reason..."></textarea>
                    </div>
                </div>

                <div class="bg-blue-600 rounded-xl shadow-lg shadow-blue-100 p-6 text-white text-center">
                    <i data-lucide="truck" class="w-8 h-8 mx-auto mb-3 opacity-80"></i>
                    <h3 class="text-sm font-bold uppercase tracking-wider">Internal Shipment</h3>
                    <p class="text-[10px] opacity-70 mt-1">Movement of stock between verified internal warehouses</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function transferForm() {
        return {
            lines: [],
            from_store_id: '',
            to_store_id: '',
            
            addItem(item) {
                if(this.lines.find(l => l.item_id === item.id)) return;
                this.lines.push({
                    item_id: item.id,
                    name: item.name,
                    sku: item.sku,
                    quantity: 1
                });
                this.$nextTick(() => lucide.createIcons());
            },

            removeItem(index) {
                this.lines.splice(index, 1);
            },

            submitForm() {
                if(!this.from_store_id || !this.to_store_id) {
                    alert('Please select source and destination stores');
                    return;
                }
                if(this.from_store_id == this.to_store_id) {
                    alert('Source and destination must be different');
                    return;
                }
                if(this.lines.length === 0) {
                    alert('Please add at least one item');
                    return;
                }
                document.getElementById('transfer-form').submit();
            }
        }
    }
</script>
@endsection

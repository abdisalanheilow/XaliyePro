<header class="h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <!-- Branch Selector -->
        <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg">
            <i data-lucide="building-2" class="w-4 h-4 text-gray-500"></i>
            <span class="text-sm font-medium text-gray-700">Branch</span>
            <select class="text-sm font-semibold text-gray-900 bg-transparent border-none focus:outline-none">
                <option>Main Office</option>
                <option>Downtown Branch</option>
                <option>Uptown Branch</option>
            </select>
        </div>

        <!-- Store Selector -->
        <div class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg">
            <i data-lucide="store" class="w-4 h-4 text-gray-500"></i>
            <span class="text-sm font-medium text-gray-700">Store</span>
            <select class="text-sm font-semibold text-gray-900 bg-transparent border-none focus:outline-none">
                <option>Main Store</option>
                <option>Warehouse A</option>
                <option>Warehouse B</option>
            </select>
        </div>

        <!-- View All Branches Checkbox -->
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375]">
            <span class="text-sm text-gray-700">View All Branches</span>
        </label>
    </div>
    <!-- Right Actions -->
    <div class="flex items-center gap-3">
        <button class="p-2 hover:bg-gray-100 rounded-lg relative group">
            <i data-lucide="help-circle" class="w-5 h-5 text-gray-600 transition-colors group-hover:text-[#28A375]"></i>
        </button>
        <button class="p-2 hover:bg-gray-100 rounded-lg relative group">
            <i data-lucide="shopping-bag"
                class="w-5 h-5 text-gray-600 transition-colors group-hover:text-[#28A375]"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-[#28A375] rounded-full"></span>
        </button>
        <button class="p-2 hover:bg-gray-100 rounded-lg relative group">
            <i data-lucide="bell" class="w-5 h-5 text-gray-600 transition-colors group-hover:text-[#28A375]"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- Profile Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <div @click="open = !open" @click.away="open = false"
                class="flex items-center gap-3 p-1.5 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                <div
                    class="w-9 h-9 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-full flex items-center justify-center text-white shadow-sm font-bold overflow-hidden">
                    @if (auth()->user()->photo)
                        <img src="{{ asset('upload/admin_images/' . auth()->user()->photo) }}" alt="Avatar"
                            class="w-full h-full object-cover">
                    @else
                        {{ substr(auth()->user()->name ?? 'Admin', 0, 2) }}
                    @endif
                </div>
                <div class="text-left hidden md:block">
                    <p class="text-sm font-bold text-gray-900 leading-none mb-1 uppercase">
                        {{ auth()->user()->name ?? 'Admin User' }}
                    </p>
                    <p class="text-xs text-[#64748B] font-medium">{{ auth()->user()->role->name ?? 'Administrator' }}
                    </p>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform"
                    :class="open ? 'rotate-180' : ''"></i>
            </div>

            <!-- Dropdown Menu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-xl py-2 z-50 origin-top-right">

                <div class="px-4 py-2 border-b border-gray-100 mb-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</p>
                </div>

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                    <span>My Profile</span>
                </a>

                <a href="{{ route('lock-screen') }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="lock" class="w-4 h-4 text-gray-400"></i>
                    <span>Lock Screen</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="settings" class="w-4 h-4 text-gray-400"></i>
                    <span>Settings</span>
                </a>

                <div class="border-t border-gray-100 mt-1 pt-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</header>

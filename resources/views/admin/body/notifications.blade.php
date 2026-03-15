@if (session()->has('message') || session()->has('status'))
    @php
        $message = session()->get('message') ?? session()->get('status');
        $status = session()->get('status');
        $alertType = session()->get('alert-type', 'success');

        if ($status === 'profile-updated') {
            $message = 'Profile updated successfully';
            $alertType = 'success';
        } elseif ($status === 'password-updated') {
            $message = 'Password changed successfully';
            $alertType = 'success';
        } elseif ($status === 'verification-link-sent') {
            $message = 'Verification link sent';
            $alertType = 'info';
        }
    @endphp

    @if ($alertType === 'success')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="check" class="w-6 h-6 text-emerald-600"></i>
                </div>
                <div>
                    @php
                        $sessionTitle = session()->get('title');
                        if (!$sessionTitle) {
                            if (request()->is('settings/*') || request()->is('profile*')) {
                                $sessionTitle = 'Settings Management';
                            } elseif (request()->is('products/*')) {
                                $sessionTitle = 'Products & Services';
                            } elseif (request()->is('contacts/*')) {
                                $sessionTitle = 'Contact Management';
                            } elseif (request()->is('accounts/*') || request()->is('journal/*')) {
                                $sessionTitle = 'Financial Records';
                            } elseif (request()->is('branches*') || request()->is('stores*')) {
                                $sessionTitle = 'Branch Management';
                            } else {
                                $sessionTitle = 'Success';
                            }
                        }
                    @endphp
                    <h4 class="text-sm font-bold text-emerald-900">{{ $sessionTitle }}</h4>
                    <p class="text-xs text-emerald-600">{{ $message }}</p>
                </div>
            </div>
            <button @click="show = false"
                class="p-2 text-emerald-400 hover:text-emerald-600 hover:bg-emerald-100 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        </script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: '{{ $alertType }}',
                    title: '{{ $message }}'
                });
            });
        </script>
    @endif
@endif

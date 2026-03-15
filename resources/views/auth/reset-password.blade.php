<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - XaliyePro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Brand Colors */
        :root {
            --primary: #28A375;
            --secondary: #2B352F;
        }

        body {
            background-color: #2B352F;
        }

        .btn-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>

<body class="overflow-hidden">
    <div class="h-screen flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-[#28A375] rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">XaliyePro</h1>
            <p class="text-lg text-gray-300">Secure your business account</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
                    <p class="text-sm text-gray-500 mt-1">Please enter your new security credentials</p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5" x-data="{ loading: false }"
                    @submit="loading = true">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                                required autofocus autocomplete="username"
                                class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent text-sm"
                                placeholder="you@example.com">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent text-sm"
                                placeholder="••••••••">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                            New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="shield-check" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                autocomplete="new-password"
                                class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent text-sm"
                                placeholder="••••••••">
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="loading"
                        class="w-full py-2.5 px-4 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#28A375] transition-all flex items-center justify-center gap-2">
                        <span x-show="!loading">Reset Password</span>
                        <div x-show="loading" class="flex items-center gap-2" style="display: none;">
                            <div class="btn-spinner"></div>
                            <span>Updating...</span>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-400">© 2026 XaliyePro. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>

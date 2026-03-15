<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - XaliyePro</title>
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
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>

<body class="overflow-hidden">
    <div class="h-screen flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-[#28A375] rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="briefcase" class="w-8 h-8 text-white"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl font-bold text-white mb-2">XaliyePro</h1>
            <p class="text-lg text-gray-300 mb-4">Enterprise Resource Planning</p>

            <!-- Security Badge -->
            <div class="flex items-center justify-center gap-2 text-gray-400">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span class="text-sm">Secure & Professional Business Management</span>
            </div>
        </div>

        <!-- Sign In Card -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <!-- Card Header -->
                <div class="mb-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Sign In</h2>
                    <p class="text-sm text-gray-500 mt-1">Welcome back! Please enter your credentials</p>
                </div>

                <!-- Sign In Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ loading: false }"
                    @submit="loading = true">
                    @csrf
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="username"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent text-sm"
                            placeholder="you@example.com">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent text-sm pr-10"
                                placeholder="Enter your password">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="togglePassword()">
                                <i data-lucide="eye" class="w-5 h-5 text-gray-400" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="h-4 w-4 text-[#28A375] focus:ring-[#28A375] border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>
                        @if (\Illuminate\Support\Facades\Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-medium text-[#28A375] hover:text-[#229967]">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" :disabled="loading"
                        class="w-full py-2.5 px-4 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#28A375] transition-all flex items-center justify-center gap-2">
                        <span x-show="!loading">Sign In</span>
                        <div x-show="loading" class="flex items-center gap-2" style="display: none;">
                            <div class="btn-spinner"></div>
                            <span>Signing In...</span>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-sm text-gray-400">© 2026 XaliyePro. All rights reserved.</p>
                <p class="text-sm text-gray-500 mt-1">Professional Business Management Solution</p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }

            lucide.createIcons();
        }
    </script>
</body>

</html>

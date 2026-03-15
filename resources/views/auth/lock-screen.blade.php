<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lock Screen - XaliyePro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #2B352F;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-sm flex flex-col items-center" x-data="{ showPassword: false, loading: false }">

        <!-- Top Icon -->
        <div class="w-16 h-16 bg-[#28A375] rounded-2xl flex items-center justify-center mb-5 shadow-lg">
            <i data-lucide="lock" class="w-8 h-8 text-white"></i>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-extrabold text-white mb-1">Screen Locked</h1>
        <p class="text-gray-400 text-sm mb-8">Your session is locked for security</p>

        <!-- Card -->
        <div class="w-full bg-white rounded-2xl shadow-2xl p-8">

            <!-- Error Message -->
            @if (session('lock_error'))
                <div
                    class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                    <span>{{ session('lock_error') }}</span>
                </div>
            @endif

            <!-- Avatar -->
            <div class="flex flex-col items-center mb-6">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-[#28A375] to-[#1a7a57] rounded-full flex items-center justify-center text-white font-bold text-xl shadow-md mb-3 overflow-hidden">
                    @if (auth()->user()->photo)
                        <img src="{{ asset('upload/admin_images/' . auth()->user()->photo) }}" alt="Avatar"
                            class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name ?? 'Admin', 0, 2)) }}
                    @endif
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ auth()->user()->name ?? 'Admin User' }}</h2>
                <p class="text-sm text-gray-500">{{ auth()->user()->email ?? '' }}</p>
            </div>

            <!-- Unlock Form -->
            <form method="POST" action="{{ route('lock-screen.unlock') }}" @submit="loading = true" class="space-y-4">
                @csrf

                <!-- Password Field -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password"
                            placeholder="Enter your password" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all text-sm">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" class="w-5 h-5" x-show="!showPassword"></i>
                            <i data-lucide="eye-off" class="w-5 h-5" x-show="showPassword" x-cloak></i>
                        </button>
                    </div>
                </div>

                <!-- Unlock Button -->
                <button type="submit" :disabled="loading"
                    class="w-full bg-[#28A375] hover:bg-[#229967] active:bg-[#1a7a57] text-white font-bold py-3 px-4 rounded-xl shadow-md transition-all flex items-center justify-center gap-2 disabled:opacity-70">
                    <template x-if="!loading">
                        <div class="flex items-center gap-2">
                            <i data-lucide="lock-open" class="w-5 h-5"></i>
                            <span>Unlock</span>
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Verifying...</span>
                        </div>
                    </template>
                </button>
            </form>

            <!-- Switch Account -->
            <div class="mt-5 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-[#28A375] hover:text-[#1a7a57] text-sm font-medium transition-colors">
                        Sign in as a different user
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-gray-500 text-xs">
            &copy; {{ date('Y') }} XaliyePro. All rights reserved.
        </p>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>

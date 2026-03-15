<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - XaliyePro</title>
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
                    <i data-lucide="mail-check" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">XaliyePro</h1>
            <p class="text-lg text-gray-300">Identity verification required</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl p-8 text-center text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Check your inbox</h2>
                <p class="text-sm text-gray-500 mb-6 px-4 leading-relaxed">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on
                    the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div
                        class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-center justify-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>A new verification link has been sent to your email.</span>
                    </div>
                @endif

                <div class="flex flex-col gap-4">
                    <form method="POST" action="{{ route('verification.send') }}" x-data="{ loading: false }"
                        @submit="loading = true">
                        @csrf
                        <button type="submit" :disabled="loading"
                            class="w-full py-2.5 px-4 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                            <span x-show="!loading">Resend Verification Email</span>
                            <div x-show="loading" class="flex items-center gap-2" style="display: none;">
                                <div class="btn-spinner"></div>
                                <span>Sending...</span>
                            </div>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-gray-400 hover:text-red-500 font-medium transition-colors">
                            Log Out
                        </button>
                    </form>
                </div>
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

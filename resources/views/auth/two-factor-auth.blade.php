<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - XaliyePro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Brand Colors */
        :root {
            --primary: #28A375;
            --secondary: #2B352F;
        }

        body {
            background-color: #2B352F;
        }

        /* Code Input Styling */
        .code-input {
            width: 3rem;
            height: 3rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .code-input:focus {
            outline: none;
            border-color: #28A375;
            ring: 2px;
            ring-color: #28A375;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
</head>

<body class="overflow-hidden">
    <div class="h-screen flex flex-col items-center justify-center py-6 px-4 sm:px-6 lg:px-8 overflow-y-auto">
        <!-- Logo & Branding -->
        <div class="text-center mb-8">
            <!-- Shield Icon -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-[#28A375] rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-4xl font-bold text-white mb-2">Two-Factor Authentication</h1>
            <p class="text-lg text-gray-300">Verify your identity to continue</p>
        </div>

        <!-- 2FA Card -->
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <!-- Card Header -->
                <div class="mb-6 text-center">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Enter Verification Code</h2>
                    <p class="text-sm text-gray-500">We've sent a 6-digit code to your registered device</p>
                </div>

                <!-- 2FA Form -->
                <form class="space-y-6" action="{{ route('dashboard') }}"
                    onsubmit="event.preventDefault(); handleVerification();">
                    @csrf
                    <!-- Code Input Boxes -->
                    <div class="flex justify-center gap-3">
                        <input type="text" maxlength="1"
                            class="code-input border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375]"
                            id="code1" oninput="moveToNext(this, 'code2')"
                            onkeydown="handleBackspace(event, this, null)">
                        <input type="text" maxlength="1"
                            class="code-input border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375]"
                            id="code2" oninput="moveToNext(this, 'code3')"
                            onkeydown="handleBackspace(event, this, 'code1')">
                        <input type="text" maxlength="1"
                            class="code-input border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375]"
                            id="code3" oninput="moveToNext(this, 'code4')"
                            onkeydown="handleBackspace(event, this, 'code2')">
                        <input type="text" maxlength="1"
                            class="code-input border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375]"
                            id="code4" oninput="moveToNext(this, 'code5')"
                            onkeydown="handleBackspace(event, this, 'code3')">
                        <input type="text" maxlength="1"
                            class="code-input border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375]"
                            id="code5" oninput="moveToNext(this, 'code6')"
                            onkeydown="handleBackspace(event, this, 'code4')">
                        <input type="text" maxlength="1"
                            class="code-input border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375]"
                            id="code6" oninput="moveToNext(this, null)"
                            onkeydown="handleBackspace(event, this, 'code5')">
                    </div>

                    <!-- Hidden Input to store full code for submission -->
                    <input type="hidden" name="code" id="fullCode">

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                            <p class="text-sm text-blue-800">Enter the 6-digit code from your authenticator app or the
                                code sent to your device.</p>
                        </div>
                    </div>

                    <!-- Verify Button -->
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 py-2.5 px-4 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#28A375] transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Verify Code
                    </button>

                    <!-- Resend Code -->
                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            Didn't receive the code?
                            <button type="button" onclick="resendCode()"
                                class="font-medium text-[#28A375] hover:text-[#229967] ml-1">
                                Resend Code
                            </button>
                        </p>
                    </div>

                    <!-- Back to Login -->
                    <div class="text-center pt-4 border-t">
                        <a href="{{ route('login') }}"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 inline-flex items-center gap-1">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            Back to Login
                        </a>
                    </div>
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

        // Auto-focus first input
        document.getElementById('code1')?.focus();

        // Move to next input
        function moveToNext(current, nextFieldId) {
            if (current.value.length === 1 && nextFieldId) {
                document.getElementById(nextFieldId).focus();
            }
        }

        // Handle backspace
        function handleBackspace(event, current, prevFieldId) {
            if (event.key === 'Backspace' && current.value === '' && prevFieldId) {
                event.preventDefault();
                document.getElementById(prevFieldId).focus();
            }
        }

        // Handle verification
        function handleVerification() {
            const code =
                document.getElementById('code1').value +
                document.getElementById('code2').value +
                document.getElementById('code3').value +
                document.getElementById('code4').value +
                document.getElementById('code5').value +
                document.getElementById('code6').value;

            if (code.length !== 6) {
                alert('Please enter the complete 6-digit code');
                return;
            }

            document.getElementById('fullCode').value = code;

            // In a real implementation, you would submit the form here
            // For now, we simulate success
            alert('Code verified successfully!');
            window.location.href = "{{ route('dashboard') }}";
        }

        // Resend code
        function resendCode() {
            alert('Verification code has been resent to your device');
            // Clear inputs
            for (let i = 1; i <= 6; i++) {
                const el = document.getElementById('code' + i);
                if (el) el.value = '';
            }
            document.getElementById('code1')?.focus();
        }

        // Only allow numbers
        document.querySelectorAll('.code-input').forEach(input => {
            input.addEventListener('keypress', function (e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>

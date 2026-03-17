<!DOCTYPE html>
<html lang="en">

<!-- XaliyePro v1.0.1 - Fresh Layout Build -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - XaliyePro')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <script>
        window.ERP_CONFIG = {
            currency_symbol: '{{ $companySettings?->default_currency ?? "$" }}',
            decimal_precision: {{ $companySettings?->decimal_precision ?? 2 }},
            date_format: '{{ $companySettings?->date_format ?? "YYYY-MM-DD" }}'
        };
    </script>
    <style>
        * {
            font-family: 'Outfit', sans-serif;
        }

        /* Brand Colors */
        :root {
            --primary: #28A375;
            --secondary: #2B352F;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #28A37540;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #28A375;
        }

        [x-cloak] {
            display: none;
        }

        /* TomSelect Tailwind Overrides */
        .ts-control {
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .ts-control>input {
            font-size: 0.875rem !important;
            font-family: inherit !important;
        }

        .ts-dropdown {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .ts-dropdown .ts-dropdown-content {
            max-height: 280px !important;
        }

        .ts-dropdown .active {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
        }

        .ts-dropdown .option {
            padding: 0.5rem 1rem;
        }

        /* Loading Spinner */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-loading {
            position: relative;
            color: transparent !important;
            pointer-events: none;
            opacity: 0.9;
        }

        .btn-loading::after {
            content: attr(data-loading-text);
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            color: white;
            display: flex;
            align-items: center;
            white-space: nowrap;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .btn-loading-spinner {
            position: absolute;
            left: calc(50% - 4rem);
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            animation: spin 0.6s linear infinite;
        }

        /* Icon button loading state */
        .btn-loading-icon {
            opacity: 0.5;
            pointer-events: none;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    @stack('css')
</head>

<body class="bg-gray-100" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden bg-gray-50">
        <div x-show="sidebarOpen" x-cloak x-transition:opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>
        @include('admin.body.sidebar')
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            @include('admin.body.header')
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="p-4 lg:p-6 pb-20">
                    @include('admin.body.notifications')
                    @yield('admin')
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Global Delete Confirmation
        window.confirmDelete = function(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28A375',
                cancelButtonColor: '#EF4444',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg px-6 py-2.5',
                    cancelButton: 'rounded-lg px-6 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        };

        // Global Form Loading State Handler
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');

            if (submitBtn && !submitBtn.classList.contains('no-loader')) {
                // Determine if it's a primary button or an icon button
                const isSmallIcon = submitBtn.offsetWidth < 50 || submitBtn.classList.contains('p-1.5');

                if (isSmallIcon) {
                    submitBtn.classList.add('btn-loading-icon');
                    return;
                }

                // If it's a delete form, use 'Deleting...', otherwise 'Saving...'
                const defaultText = form.querySelector('input[name="_method"]')?.value === 'DELETE' ? 'Deleting...' : 'Saving...';
                const loadingText = submitBtn.getAttribute('data-loading-text') || defaultText;

                // Store original content
                if (!submitBtn.getAttribute('data-original-content')) {
                    submitBtn.setAttribute('data-original-content', submitBtn.innerHTML);
                }

                // Add loading state
                submitBtn.setAttribute('data-loading-text', loadingText);
                submitBtn.classList.add('btn-loading');

                // Inject SVG spinner from XaliyePro Design System
                const svg = `<svg class="btn-loading-spinner" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
                submitBtn.insertAdjacentHTML('afterbegin', svg);
            }
        });

        // Reset loaders logic
        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.btn-loading').forEach(btn => {
                btn.classList.remove('btn-loading');
                btn.removeAttribute('data-loading-text');
                const spinner = btn.querySelector('.btn-loading-spinner');
                if (spinner) spinner.remove();
            });
            document.querySelectorAll('.btn-loading-icon').forEach(btn => {
                btn.classList.remove('btn-loading-icon');
            });
        });

        // Global Context Handler (Branch/Store switching)
        window.updateContext = function(data) {
            fetch('{{ route("session.context.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error updating context:', error);
            });
        };
    </script>

    @stack('scripts')
</body>

</html>

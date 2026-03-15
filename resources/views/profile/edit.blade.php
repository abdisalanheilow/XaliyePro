@extends('admin.admin_master')
@section('title', 'My Profile - XaliyePro')
@section('admin')
    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-500 mt-1">Manage your account settings and preferences</p>
    </div>

    <!-- Profile Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Picture Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-6">Profile Picture</h3>

                <div class="flex flex-col items-center">
                    <!-- Avatar with Camera Icon -->
                    <div class="relative mb-4">
                        <div id="imageDisplay"
                            class="w-32 h-32 rounded-full flex items-center justify-center text-white shadow-lg overflow-hidden border-4 border-white bg-gradient-to-br from-[#28A375] to-[#229967]">
                            @if (auth()->user()->photo)
                                <img id="showImage" src="{{ asset('upload/admin_images/' . auth()->user()->photo) }}"
                                    alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <span class="text-5xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <label for="image"
                            class="absolute bottom-0 right-0 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md border-2 border-white cursor-pointer hover:bg-gray-50 transition-colors">
                            <i data-lucide="camera" class="w-5 h-5 text-gray-600"></i>
                        </label>
                    </div>

                    <!-- User Info -->
                    <h4 class="font-bold text-gray-900 text-lg">{{ auth()->user()->name }}</h4>
                    <p class="text-sm text-gray-500 mt-1">{{ auth()->user()->email }}</p>

                    <!-- Change Picture Button -->
                    <button type="button" onclick="document.getElementById('image').click()"
                        class="mt-6 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors w-full">
                        Change Picture
                    </button>
                </div>
            </div>
        </div>

        <!-- Personal Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-6">Personal Information</h3>

                <form id="profileUpdateForm" method="post" action="{{ route('profile.update') }}" class="space-y-5"
                    x-data="{ loading: false }" @submit="loading = true" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    <!-- Hidden File Input -->
                    <input type="file" name="photo" id="image" class="hidden" accept="image/*">

                    <!-- Full Name and Email Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}"
                                required autofocus autocomplete="name"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                            @if ($errors->has('name'))
                                <p class="mt-1 text-xs text-red-600">{{ $errors->first('name') }}</p>
                            @endif
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}"
                                required autocomplete="username"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                            @if ($errors->has('email'))
                                <p class="mt-1 text-xs text-red-600">{{ $errors->first('email') }}</p>
                            @endif

                            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-sm text-gray-800">
                                        {{ __('Your email address is unverified.') }}
                                        <button form="send-verification"
                                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-start gap-3 pt-4">
                        <button type="submit" :disabled="loading"
                            class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#28A375] transition-all flex items-center justify-center gap-2">
                            <span x-show="!loading">Save Changes</span>
                            <div x-show="loading" class="flex items-center gap-2" style="display: none;">
                                <div class="btn-spinner"></div>
                                <span>Saving...</span>
                            </div>
                        </button>
                    </div>
                </form>
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-6">Change Password</h3>

            <form method="post" action="{{ route('password.update') }}" class="space-y-5" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf
                @method('put')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Current Password -->
                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input id="update_password_current_password" name="current_password" type="password"
                            placeholder="Enter current password" autocomplete="current-password"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                        @if ($errors->updatePassword->has('current_password'))
                            <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input id="update_password_password" name="password" type="password"
                            placeholder="Enter new password" autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                        @if ($errors->updatePassword->has('password'))
                            <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label for="update_password_password_confirmation"
                            class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                            placeholder="Confirm new password" autocomplete="new-password"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                        @if ($errors->updatePassword->has('password_confirmation'))
                            <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('password_confirmation') }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-start gap-3 pt-4">
                    <button type="submit" :disabled="loading"
                        class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#28A375] transition-all flex items-center justify-center gap-2">
                        <span x-show="!loading">Update Password</span>
                        <div x-show="loading" class="flex items-center gap-2" style="display: none;">
                            <div class="btn-spinner"></div>
                            <span>Updating...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById('image').addEventListener('change', function (e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const imageDisplay = document.getElementById('imageDisplay');
                    imageDisplay.innerHTML = '<img id="showImage" src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
@endsection

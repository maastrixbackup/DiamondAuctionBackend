<!-- In your <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
<x-guest-layout>
    <!-- Session Status -->
    @if (session('success'))
        <div class="alert alert-success  rounded-3 shadow-sm"
            style="color: green;padding: 13px 13px;
    background: cornsilk;
    border-radius: 5px;" id="success-alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger  rounded-3 shadow-sm "
            style="color: red;padding: 13px 13px;
    background: cornsilk;
    border-radius: 5px;" id="success-alert">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" aria-placeholder="Email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="flex items-center border rounded-md overflow-hidden mt-1">
                <x-text-input id="password" class="flex-1 border-0 rounded-none" type="password" name="password"
                    required autocomplete="current-password" placeholder="Password" />

                <!-- Toggle Button -->
                <button type="button" id="togglePassword"
                    class="px-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                    <i class="fas fa-eye-slash hidden" id="eyeSlashIcon"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>




        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('admin.password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    window.onload = function() {
        let alert = document.getElementById('success-alert');
        if (alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }
    };
</script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const input = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        const eyeSlash = document.getElementById('eyeSlashIcon');

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        eye.classList.toggle('hidden', !isHidden);
        eyeSlash.classList.toggle('hidden', isHidden);
    });
</script>

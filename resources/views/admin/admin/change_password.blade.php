@extends('admin.layouts.app')
@section('title', 'Change Password')

@section('content')
    <div class="page-inner mt-4 pt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Change Password</h4>
            </div>
            <div class="card-body">

                @if (session('success'))
                    <div class="alert alert-success  mx-4 mt-3 rounded-3 shadow-sm" id="success-alert">
                        {{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger  mx-4 mt-3 rounded-3 shadow-sm" id="success-alert">{{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.changePassword.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" autofocus required>
                        @error('current_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                        @error('new_password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label>Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <a href="{{ route('admin.profile') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

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

    @if (session('logout_redirect'))
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
        <script>
            document.getElementById('logout-form').submit();
        </script>
    @endif

@endsection

@extends('admin.layouts.app')
@section('title', 'Profile')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Profile</h3>
                    <ul class="breadcrumbs d-flex align-items-center mb-0">
                        <li class="nav-home me-2">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="icon-home"></i>
                            </a>
                        </li>
                        <li class="separator me-2">
                            <i class="icon-arrow-right"></i>
                        </li>
                        <li class="nav-item">Profile</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Profile Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <td>{{ $admin->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $admin->email }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>
                                    @if ($admin->role === 'superadmin')
                                        <span class="badge bg-success">Superadmin</span>
                                    @else
                                        <span class="badge bg-primary">Admin</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.app')
@section('title', 'Seller List')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Sellers</h3>
                    <ul class="breadcrumbs d-flex align-items-center mb-0">
                        <li class="nav-home me-2">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="icon-home"></i>
                            </a>
                        </li>
                        <li class="separator me-2">
                            <i class="icon-arrow-right"></i>
                        </li>
                        <li class="nav-item">
                            <a href="#">Sellers</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">All Sellers</h4>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success" id="success-alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>KYC Status</th>
                                            <th>Account Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sellers as $seller)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $seller->type == 1 ? 'Company' : 'Individual' }}</td>
                                                <td>{{ $seller->full_name }}</td>
                                                <td>{{ $seller->email_address }}</td>
                                                <td>{{ $seller->phone_number }}</td>
                                                {{-- <td>
                                                    @if ($seller->status == 0)
                                                        <a href="{{ route('admin.change-seller-status', $seller->id) }}"
                                                            class="badge text-decoration-none badge-sm mt-1 btn-danger"
                                                            title="Change Status"
                                                            onclick="return confirm('Are you sure to change status?')">
                                                            Pending
                                                        </a>
                                                    @else
                                                        <a href="{{ route('admin.change-seller-status', $seller->id) }}"
                                                            class="badge text-decoration-none badge-sm mt-1 btn-success"
                                                            title="Change Status"
                                                            onclick="return confirm('Are you sure to change status?')">
                                                            Approved
                                                        </a>
                                                    @endif
                                                </td> --}}
                                                <!-- KYC Status -->
                                                <td>
                                                    <a href="{{ route('admin.change-seller-kyc-status', $seller->id) }}"
                                                        class="badge text-decoration-none badge-sm
                                                        {{ $seller->kyc_status == 1 ? 'bg-success' : ($seller->kyc_status == 2 ? 'bg-danger' : 'bg-warning') }}"
                                                        title="Change KYC Status"
                                                        onclick="return confirm('Are you sure to change KYC status?')">
                                                        {{ $seller->kyc_status == 1 ? 'Approved' : ($seller->kyc_status == 2 ? 'Rejected' : 'Pending') }}
                                                    </a>
                                                </td>

                                                <!-- Account Status -->
                                                <td>
                                                    <a href="{{ route('admin.change-seller-account-status', $seller->id) }}"
                                                        class="badge text-decoration-none badge-sm
                                                        {{ $seller->account_status == 1 ? 'bg-success' : ($seller->account_status == 2 ? 'bg-danger' : 'bg-warning') }}"
                                                        title="Change Account Status"
                                                        onclick="return confirm('Are you sure to change Account status?')">
                                                        {{ $seller->account_status == 1 ? 'Active' : ($seller->account_status == 2 ? 'Suspended' : 'Pending') }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.sellerDetails', $seller->id) }}"
                                                        class="btn btn-sm btn-primary" title="View">
                                                        <i class="icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
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
@endpush

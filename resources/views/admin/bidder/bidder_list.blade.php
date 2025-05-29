@extends('admin.layouts.app')
@section('title', 'Bidder List')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Bidders</h3>
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
                            <a href="#">Bidders</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">All Bidders</h4>
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
                                        @foreach ($bidders as $bidder)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $bidder->type == 1 ? 'Company' : 'Individual' }}</td>
                                                <td>{{ $bidder->full_name }}</td>
                                                <td>{{ $bidder->email_address }}</td>
                                                <td>{{ $bidder->phone_number }}</td>

                                                <!-- KYC Status -->
                                                <td>
                                                    <a href="{{ route('admin.change-bidder-kyc-status', $bidder->id) }}"
                                                        class="badge text-decoration-none badge-sm
                                                        {{ $bidder->kyc_status == 1 ? 'bg-success' : ($bidder->kyc_status == 2 ? 'bg-danger' : 'bg-warning') }}"
                                                        title="Change KYC Status"
                                                        onclick="return confirm('Are you sure to change KYC status?')">
                                                        {{ $bidder->kyc_status == 1 ? 'Approved' : ($bidder->kyc_status == 2 ? 'Rejected' : 'Pending') }}
                                                    </a>
                                                </td>

                                                <!-- Account Status -->
                                                <td>
                                                    <a href="{{ route('admin.change-bidder-account-status', $bidder->id) }}"
                                                        class="badge text-decoration-none badge-sm
                                                        {{ $bidder->account_status == 1 ? 'bg-success' : ($bidder->account_status == 2 ? 'bg-danger' : 'bg-warning') }}"
                                                        title="Change Account Status"
                                                        onclick="return confirm('Are you sure to change Account status?')">
                                                        {{ $bidder->account_status == 1 ? 'Active' : ($bidder->account_status == 2 ? 'Suspended' : 'Pending') }}
                                                    </a>
                                                </td>

                                                <!-- View Action -->
                                                <td>
                                                    <a href="{{ route('admin.bidderDetails', $bidder->id) }}"
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

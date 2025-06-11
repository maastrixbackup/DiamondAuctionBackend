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
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-4">
                <h4 class="card-title mb-0 fw-semibold">All Sellers</h4>
            </div>

            @if (session('success'))
                <div class="alert alert-success mx-4 mt-3 rounded-3 shadow-sm" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="basic-datatables" class="table table-hover align-middle text-nowrap table-bordered rounded-3 overflow-hidden">
                        <thead class="table-light">
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

                                    <!-- KYC Status -->
                                    <td>
                                        <span class="badge rounded-pill
                                            {{ $seller->kyc_status == 1 ? 'px-3 py-2 border border-success bg-success-subtle text-success-emphasis' :
                                                ($seller->kyc_status == 2 ? 'px-3 py-2 border border-danger bg-danger-subtle text-danger-emphasis' : 'px-3 py-2 border border-warning bg-warning-subtle text-warning-emphasis') }}">
                                            {{ $seller->kyc_status == 1 ? 'Approved' :
                                                ($seller->kyc_status == 2 ? 'Rejected' : 'Pending') }}
                                        </span>
                                    </td>

                                    <!-- Account Status -->
                                    <td>
                                        <span class="badge rounded-pill
                                            {{ $seller->account_status == 1 ? 'px-3 py-2 border border-success bg-success-subtle text-success-emphasis' :
                                                ($seller->account_status == 2 ? 'px-3 py-2 border border-danger bg-danger-subtle text-danger-emphasis' : 'px-3 py-2 border border-warning bg-warning-subtle text-warning-emphasis') }}">
                                            {{ $seller->account_status == 1 ? 'Active' :
                                                ($seller->account_status == 2 ? 'Suspended' : 'Pending') }}
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td>
                                        <a href="{{ route('admin.sellerDetails', $seller->id) }}"
                                           class="btn btn-sm btn-outline-primary d-inline-flex align-items-center" title="View">
                                            <i class="icon-eye me-1"></i> View
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

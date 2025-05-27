@extends('admin.layouts.app')
@section('title', 'View Seller')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Seller Details</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item"><a href="{{ route('admin.seller') }}">Sellers</a></li>
                    <li class="separator"><i class="icon-arrow-right"></i></li>
                    <li class="nav-item">View Seller</li>
                </ul>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Seller Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Type</th>
                                <td>{{ $seller->type == 1 ? 'Company' : 'Individual' }}</td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td>{{ $seller->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $seller->email_address }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $seller->phone_number }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $seller->country }}</td>
                            </tr>

                            @if ($seller->type == 1)
                                {{-- Company --}}
                                <tr>
                                    <th>Company Name</th>
                                    <td>{{ $seller->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Registration Number</th>
                                    <td>{{ $seller->registration_number }}</td>
                                </tr>
                                <tr>
                                    <th>Director Name</th>
                                    <td>{{ $seller->director_name }}</td>
                                </tr>
                                <tr>
                                    <th>Director Email</th>
                                    <td>{{ $seller->director_email }}</td>
                                </tr>
                                <tr>
                                    <th>Director Phone</th>
                                    <td>{{ $seller->director_phone }}</td>
                                </tr>

                                {{-- Company Documents --}}
                                <tr>
                                    <th>Certificate of Incorporation</th>
                                    <td>
                                        @if ($seller->certificate_of_incorporation)
                                            <a href="{{ asset('storage/document/seller/' . $seller->certificate_of_incorporation) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="Click to view">View
                                                Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Valid Trade License</th>
                                    <td>
                                        @if ($seller->valid_trade_license)
                                            <a href="{{ asset('storage/document/seller/' . $seller->valid_trade_license) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="Click to view">View
                                                Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Passport Copy (Authorised)</th>
                                    <td>
                                        @if ($seller->passport_copy_authorised)
                                            <a href="{{ asset('storage/document/seller/' . $seller->passport_copy_authorised) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="Click to view">View
                                                Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>UBO Declaration</th>
                                    <td>
                                        @if ($seller->ubo_declaration)
                                            <a href="{{ asset('storage/document/seller/' . $seller->ubo_declaration) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="Click to view">View
                                                Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            @elseif($seller->type == 2)
                                {{-- Individual --}}
                                <tr>
                                    <th>Passport Copy</th>
                                    <td>
                                        @if ($seller->passport_copy)
                                            <a href="{{ asset('storage/document/seller/' . $seller->passport_copy) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="Click to view">View
                                                Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Proof of Address</th>
                                    <td>
                                        @if ($seller->proof_of_ownership)
                                            <a href="{{ asset('storage/document/seller/' . $seller->proof_of_ownership) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="Click to view">View
                                                Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th>KYC Status</th>
                                <td>
                                    @if ($seller->kyc_status == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($seller->kyc_status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($seller->kyc_status == 2)
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Account Status</th>
                                <td>
                                    @if ($seller->account_status == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($seller->account_status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($seller->account_status == 2)
                                        <span class="badge bg-danger">Suspended</span>
                                    @endif
                                </td>
                            </tr>

                            {{-- <tr>
                                <th>Status</th>
                                <td>
                                    @if ($seller->status == 0)
                                        <span class="badge bg-danger">Pending</span>
                                    @else
                                        <span class="badge bg-success">Approved</span>
                                    @endif
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>

                    <a href="{{ route('admin.seller') }}" class="btn btn-secondary mt-3">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.app')
@section('title', 'View Bidder')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Bidder Details</h3>
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
                            <a href="{{ route('admin.bidder') }}">Bidders</a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">View Bidder</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bidder Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Type</th>
                                <td>{{ $bidder->type == 1 ? 'Company' : 'Individual' }}</td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td>{{ $bidder->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $bidder->email_address }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $bidder->phone_number }}</td>
                            </tr>
                            <tr>
                                <th>Country</th>
                                <td>{{ $bidder->country }}</td>
                            </tr>

                            @if ($bidder->type == 1)
                                {{-- Company --}}
                                <tr>
                                    <th>Company Name</th>
                                    <td>{{ $bidder->company_name }}</td>
                                </tr>
                                <tr>
                                    <th>Registration Number</th>
                                    <td>{{ $bidder->registration_number }}</td>
                                </tr>
                                <tr>
                                    <th>Director Name</th>
                                    <td>{{ $bidder->director_name }}</td>
                                </tr>
                                <tr>
                                    <th>Director Email</th>
                                    <td>{{ $bidder->director_email }}</td>
                                </tr>
                                <tr>
                                    <th>Director Phone</th>
                                    <td>{{ $bidder->director_phone }}</td>
                                </tr>

                                {{-- Company Documents --}}
                                <tr>
                                    <th>Certificate of Incorporation</th>
                                    <td>
                                        @if ($bidder->certificate_of_incorporation)
                                            <a href="{{ asset('storage/document/bidder/' . $bidder->certificate_of_incorporation) }}"
                                                target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Valid Trade License</th>
                                    <td>
                                        @if ($bidder->valid_trade_license)
                                            <a href="{{ asset('storage/document/bidder/' . $bidder->valid_trade_license) }}"
                                                target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Passport Copy (Authorised)</th>
                                    <td>
                                        @if ($bidder->passport_copy_authorised)
                                            <a href="{{ asset('storage/document/bidder/' . $bidder->passport_copy_authorised) }}"
                                                target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>UBO Declaration</th>
                                    <td>
                                        @if ($bidder->ubo_declaration)
                                            <a href="{{ asset('storage/document/bidder/' . $bidder->ubo_declaration) }}"
                                                target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                {{-- Individual --}}
                                <tr>
                                    <th>Passport Copy</th>
                                    <td>
                                        @if ($bidder->passport_copy)
                                            <a href="{{ asset('storage/document/bidder/' . $bidder->passport_copy) }}"
                                                target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Proof of Address</th>
                                    <td>
                                        @if ($bidder->proof_of_ownership)
                                            <a href="{{ asset('storage/document/bidder/' . $bidder->proof_of_ownership) }}"
                                                target="_blank" class="btn btn-sm btn-primary">View Document</a>
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th>KYC Status</th>
                                <td>
                                    @if ($bidder->kyc_status == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($bidder->kyc_status == 1)
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($bidder->kyc_status == 2)
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Account Status</th>
                                <td>
                                    @if ($bidder->account_status == 0)
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($bidder->account_status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($bidder->account_status == 2)
                                        <span class="badge bg-danger">Suspended</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ route('admin.bidder') }}" class="btn btn-secondary mt-3">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection

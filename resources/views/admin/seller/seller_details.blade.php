@extends('admin.layouts.app')
@section('title', 'View Seller')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Seller Details</h3>
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
                            <a href="{{ route('admin.seller') }}">Sellers</a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">View Seller</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Seller Details</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div id="success-alert" class="alert alert-success mt-2">{{ session('success') }}</div>
                    @endif
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
                                {{-- <tr>
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
                                </tr> --}}

                                <tr>
                                    <th>Certificate of Incorporation</th>
                                    <td>
                                        @if ($seller->certificate_of_incorporation)
                                            <a href="{{ asset('storage/document/seller/' . $seller->certificate_of_incorporation) }}"
                                                target="_blank" class="btn btn-sm btn-primary"
                                                title="View Document">View</a>

                                            @if ($seller->certificate_of_incorporation_status === 0)
                                                <form action="{{ route('admin.update-seller-document-status') }}" method="POST"
                                                    style="display:inline-block;">
                                                    @csrf
                                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                    <input type="hidden" name="field"
                                                        value="certificate_of_incorporation">
                                                    <button name="status" value="1"
                                                        class="btn btn-sm btn-success">Approve</button>
                                                    <button name="status" value="2"
                                                        class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif ($seller->certificate_of_incorporation_status == 1)
                                                <span class="badge bg-success ms-2">Approved</span>
                                            @elseif ($seller->certificate_of_incorporation_status == 2)
                                                <span class="badge bg-danger ms-2">Rejected</span>
                                            @endif
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
                                                target="_blank" class="btn btn-sm btn-primary"
                                                title="View Document">View</a>

                                            @if ($seller->valid_trade_license_status === 0)
                                                <form action="{{ route('admin.update-seller-document-status') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                    <input type="hidden" name="field" value="valid_trade_license">
                                                    <button type="submit" name="status" value="1"
                                                        class="btn btn-sm btn-success">Approve</button>
                                                    <button type="submit" name="status" value="2"
                                                        class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif ($seller->valid_trade_license_status === 1)
                                                <span class="badge bg-success ms-2">Approved</span>
                                            @elseif ($seller->valid_trade_license_status === 2)
                                                <span class="badge bg-danger ms-2">Rejected</span>
                                            @endif
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
                                                target="_blank" class="btn btn-sm btn-primary"
                                                title="View Document">View</a>

                                            @if ($seller->passport_copy_authorised_status === 0)
                                                <form action="{{ route('admin.update-seller-document-status') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                    <input type="hidden" name="field" value="passport_copy_authorised">
                                                    <button type="submit" name="status" value="1"
                                                        class="btn btn-sm btn-success">Approve</button>
                                                    <button type="submit" name="status" value="2"
                                                        class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif ($seller->passport_copy_authorised_status === 1)
                                                <span class="badge bg-success ms-2">Approved</span>
                                            @elseif ($seller->passport_copy_authorised_status === 2)
                                                <span class="badge bg-danger ms-2">Rejected</span>
                                            @endif
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
                                                target="_blank" class="btn btn-sm btn-primary"
                                                title="View Document">View</a>

                                            @if ($seller->ubo_declaration_status === 0)
                                                <form action="{{ route('admin.update-seller-document-status') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                    <input type="hidden" name="field" value="ubo_declaration">
                                                    <button type="submit" name="status" value="1"
                                                        class="btn btn-sm btn-success">Approve</button>
                                                    <button type="submit" name="status" value="2"
                                                        class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif ($seller->ubo_declaration_status === 1)
                                                <span class="badge bg-success ms-2">Approved</span>
                                            @elseif ($seller->ubo_declaration_status === 2)
                                                <span class="badge bg-danger ms-2">Rejected</span>
                                            @endif
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
                                                target="_blank" class="btn btn-sm btn-primary" title="View Document"
                                                title="View Document">View</a>

                                            @if ($seller->passport_copy_status === 0)
                                                <form action="{{ route('admin.update-seller-document-status') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                    <input type="hidden" name="field" value="passport_copy">
                                                    <button type="submit" name="status" value="1"
                                                        class="btn btn-sm btn-success">Approve</button>
                                                    <button type="submit" name="status" value="2"
                                                        class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif ($seller->passport_copy_status === 1)
                                                <span class="badge bg-success ms-2">Approved</span>
                                            @elseif ($seller->passport_copy_status === 2)
                                                <span class="badge bg-danger ms-2">Rejected</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not uploaded</span>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <th>Proof of Ownership</th>
                                    <td>
                                        @if ($seller->proof_of_ownership)
                                            <a href="{{ asset('storage/document/seller/' . $seller->proof_of_ownership) }}"
                                                target="_blank" class="btn btn-sm btn-primary" title="View Document"
                                                title="View Document">View</a>

                                            @if ($seller->proof_of_ownership_status === 0)
                                                <form action="{{ route('admin.update-seller-document-status') }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                                    <input type="hidden" name="field" value="proof_of_ownership">
                                                    <button type="submit" name="status" value="1"
                                                        class="btn btn-sm btn-success">Approve</button>
                                                    <button type="submit" name="status" value="2"
                                                        class="btn btn-sm btn-danger">Reject</button>
                                                </form>
                                            @elseif ($seller->proof_of_ownership_status === 1)
                                                <span class="badge bg-success ms-2">Approved</span>
                                            @elseif ($seller->proof_of_ownership_status === 2)
                                                <span class="badge bg-danger ms-2">Rejected</span>
                                            @endif
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
                                    {{-- Status Badge --}}
                                    @if ($seller->account_status == 0)
                                        <span class="badge bg-warning ms-2">Pending</span>
                                    @elseif ($seller->account_status == 1)
                                        <span class="badge bg-success ms-2">Active</span>
                                    @elseif ($seller->account_status == 2)
                                        <span class="badge bg-danger ms-2">Suspended</span>
                                    @endif

                                    {{-- Action Buttons Inline --}}
                                    <span class="ms-3">
                                        @if ($seller->account_status !== 1)
                                            <a href="{{ route('admin.change-seller-account-status', ['id' => $seller->id, 'status' => 1]) }}"
                                                class="btn btn-sm btn-success">Set Active</a>
                                        @endif

                                        @if ($seller->account_status !== 2)
                                            <a href="{{ route('admin.change-seller-account-status', ['id' => $seller->id, 'status' => 2]) }}"
                                                class="btn btn-sm btn-danger">Set Suspended</a>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ route('admin.seller') }}" class="btn btn-secondary mt-3">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.onload = function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }
        };
    </script>
@endpush

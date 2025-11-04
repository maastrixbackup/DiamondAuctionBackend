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

            <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-light py-3 px-4">
        <h4 class="card-title mb-0 fw-semibold">Seller Details</h4>
    </div>

    <div class="card-body px-4 py-4">
        @if (session('success'))
            <div id="success-alert" class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered align-middle table-striped text-sm">
            <tbody>
                <tr>
                    <th class="w-25">Type</th>
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
                    {{-- Company Fields --}}
                    <tr><th>Company Name</th><td>{{ $seller->company_name }}</td></tr>
                    <tr><th>Registration Number</th><td>{{ $seller->registration_number }}</td></tr>
                    <tr><th>Director Name</th><td>{{ $seller->director_name }}</td></tr>
                    <tr><th>Director Email</th><td>{{ $seller->director_email }}</td></tr>
                    <tr><th>Director Phone</th><td>{{ $seller->director_phone }}</td></tr>

                    @php
                        $documents = [
                            ['certificate_of_incorporation', 'Certificate of Incorporation'],
                            ['valid_trade_license', 'Valid Trade License'],
                            ['passport_copy_authorised', 'Passport Copy (Authorised)'],
                            ['ubo_declaration', 'UBO Declaration'],
                            ['kyc_document', 'Kyc Document'],

                        ];
                    @endphp

                    @foreach ($documents as [$field, $label])
                        <tr>
                            <th>{{ $label }}</th>
                            <td>
                                @if ($seller->$field)
                                    <a href="{{ asset('storage/document/seller/' . $seller->$field) }}"
                                       target="_blank" class="btn btn-sm btn-primary">View</a>

                                    @php $statusField = $field . '_status'; @endphp
                                    @if ($seller->$statusField === 0)
                                        <form action="{{ route('admin.update-seller-document-status') }}"
                                              method="POST" class="d-inline ms-2">
                                            @csrf
                                            <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                            <input type="hidden" name="field" value="{{ $field }}">
                                            <button name="status" value="1" class="btn btn-sm btn-success">Approve</button>
                                            <button name="status" value="2" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @elseif ($seller->$statusField === 1)
                                        <span class="badge bg-success ms-2 px-3 py-2">Approved</span>
                                    @elseif ($seller->$statusField === 2)
                                        <span class="badge bg-danger ms-2 px-3 py-2">Rejected</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                @elseif ($seller->type == 2)
                    {{-- Individual Fields --}}
                    @php
                        $individualDocs = [
                            ['passport_copy', 'Passport Copy'],
                            ['proof_of_ownership', 'Proof of Ownership'],
                            ['kyc_document', 'Kyc Document'],
                        ];
                    @endphp

                    @foreach ($individualDocs as [$field, $label])
                        <tr>
                            <th>{{ $label }}</th>
                            <td>
                                @if ($seller->$field)
                                    <a href="{{ asset('storage/document/seller/' . $seller->$field) }}"
                                       target="_blank" class="btn btn-sm btn-primary">View</a>

                                    @php $statusField = $field . '_status'; @endphp
                                    @if ($seller->$statusField === 0)
                                        <form action="{{ route('admin.update-seller-document-status') }}"
                                              method="POST" class="d-inline ms-2">
                                            @csrf
                                            <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                                            <input type="hidden" name="field" value="{{ $field }}">
                                            <button name="status" value="1" class="btn btn-sm btn-success">Approve</button>
                                            <button name="status" value="2" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    @elseif ($seller->$statusField === 1)
                                        <span class="badge bg-success ms-2 px-3 py-2">Approved</span>
                                    @elseif ($seller->$statusField === 2)
                                        <span class="badge bg-danger ms-2 px-3 py-2">Rejected</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not uploaded</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif

                <tr>
                    <th>KYC Status</th>
                    <td>
                        <span class="badge
                            {{ $seller->kyc_status == 1 ? 'bg-success px-3 py-2' :
                               ($seller->kyc_status == 2 ? 'bg-danger px-3 py-2' : 'bg-warning px-3 py-2') }}">
                            {{ $seller->kyc_status == 1 ? 'Approved' :
                               ($seller->kyc_status == 2 ? 'Rejected' : 'Pending') }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Account Status</th>
                    <td>
                        <span class="badge
                            {{ $seller->account_status == 1 ? 'bg-success px-3 py-2' :
                               ($seller->account_status == 2 ? 'bg-danger px-3 py-2' : 'bg-warning px-3 py-2') }} me-3">
                            {{ $seller->account_status == 1 ? 'Active' :
                               ($seller->account_status == 2 ? 'Suspended' : 'Pending') }}
                        </span>

                        @if ($seller->account_status !== 1)
                            <a href="{{ route('admin.change-seller-account-status', ['id' => $seller->id, 'status' => 1]) }}"
                               class="btn btn-sm btn-outline-success">Set Active</a>
                        @endif
                        @if ($seller->account_status !== 2)
                            <a href="{{ route('admin.change-seller-account-status', ['id' => $seller->id, 'status' => 2]) }}"
                               class="btn btn-sm btn-outline-danger ms-2">Set Suspended</a>
                        @endif
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

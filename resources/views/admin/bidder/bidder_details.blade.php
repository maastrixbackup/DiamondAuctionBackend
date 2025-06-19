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

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light py-3 px-4">
                    <h4 class="card-title mb-0 fw-semibold">Bidder Details</h4>
                </div>

                <div class="card-body px-4 py-4">
                    @if (session('success'))
                        <div id="success-alert" class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <table class="table table-bordered table-striped align-middle text-sm">
                        <tbody>
                            <tr>
                                <th class="w-25">Type</th>
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
                                {{-- Company Info --}}
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

                                @php
                                    $companyDocs = [
                                        ['certificate_of_incorporation', 'Certificate of Incorporation'],
                                        ['valid_trade_license', 'Valid Trade License'],
                                        ['passport_copy_authorised', 'Passport Copy (Authorised)'],
                                        ['ubo_declaration', 'UBO Declaration'],
                                    ];
                                @endphp

                                @foreach ($companyDocs as [$field, $label])
                                    @php $statusField = $field . '_status'; @endphp
                                    <tr>
                                        <th>{{ $label }}</th>
                                        <td>
                                            @if ($bidder->$field)
                                                <a href="{{ asset('storage/document/bidder/' . $bidder->$field) }}"
                                                    target="_blank" class="btn btn-sm btn-primary">View</a>

                                                @if ($bidder->$statusField === 0)
                                                    <form action="{{ route('admin.update-bidder-document-status') }}"
                                                        method="POST" class="d-inline ms-2">
                                                        @csrf
                                                        <input type="hidden" name="bidder_id" value="{{ $bidder->id }}">
                                                        <input type="hidden" name="field" value="{{ $field }}">
                                                        <button name="status" value="1"
                                                            class="btn btn-sm btn-success">Approve</button>
                                                        <button name="status" value="2"
                                                            class="btn btn-sm btn-danger">Reject</button>
                                                    </form>
                                                @elseif ($bidder->$statusField === 1)
                                                    <span class="badge bg-success ms-2 px-3 py-2">Approved</span>
                                                @elseif ($bidder->$statusField === 2)
                                                    <span class="badge bg-danger ms-2 px-3 py-2">Rejected</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Not uploaded</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- Individual Docs --}}
                                @php
                                    $individualDocs = [
                                        ['passport_copy', 'Passport Copy'],
                                        ['proof_of_address', 'Proof of Address'],
                                    ];
                                @endphp

                                @foreach ($individualDocs as [$field, $label])
                                    @php $statusField = $field . '_status'; @endphp
                                    <tr>
                                        <th>{{ $label }}</th>
                                        <td>
                                            @if ($bidder->$field)
                                                <a href="{{ asset('storage/document/bidder/' . $bidder->$field) }}"
                                                    target="_blank" class="btn btn-sm btn-primary">View</a>

                                                @if ($bidder->$statusField === 0)
                                                    <form action="{{ route('admin.update-bidder-document-status') }}"
                                                        method="POST" class="d-inline ms-2">
                                                        @csrf
                                                        <input type="hidden" name="bidder_id" value="{{ $bidder->id }}">
                                                        <input type="hidden" name="field" value="{{ $field }}">
                                                        <button name="status" value="1"
                                                            class="btn btn-sm btn-success">Approve</button>
                                                        <button name="status" value="2"
                                                            class="btn btn-sm btn-danger">Reject</button>
                                                    </form>
                                                @elseif ($bidder->$statusField === 1)
                                                    <span class="badge bg-success ms-2 px-3 py-2">Approved</span>
                                                @elseif ($bidder->$statusField === 2)
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
                                <th>Account Status</th>
                                <td>
                                    <span
                                        class="badge
                            {{ $bidder->account_status == 1
                                ? 'bg-success px-3 py-2'
                                : ($bidder->account_status == 2
                                    ? 'bg-danger px-3 py-2'
                                    : 'bg-warning px-3 py-2') }} me-3">
                                        {{ $bidder->account_status == 1 ? 'Active' : ($bidder->account_status == 2 ? 'Suspended' : 'Pending') }}
                                    </span>

                                    @if ($bidder->account_status !== 1)
                                        <a href="{{ route('admin.change-bidder-account-status', ['id' => $bidder->id, 'status' => 1]) }}"
                                            class="btn btn-sm btn-outline-success">Set Active</a>
                                    @endif
                                    @if ($bidder->account_status !== 2)
                                        <a href="{{ route('admin.change-bidder-account-status', ['id' => $bidder->id, 'status' => 2]) }}"
                                            class="btn btn-sm btn-outline-danger ms-2">Set Suspended</a>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <a href="{{ route('admin.bidder') }}" class="btn btn-secondary mt-3">Back</a>
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

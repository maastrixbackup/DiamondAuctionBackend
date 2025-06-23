@extends('admin.layouts.app')
@section('title', 'Lot List')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Lots</h3>
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
                            <a href="javascript:;">Lots</a>
                        </li>
                    </ul>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.lots.index') }}" class="mb-4">
                <div class="bg-light rounded-4 px-1 py-4 d-flex flex-wrap align-items-center gap-2 shadow-sm">

                    {{-- Search Field with extra padding --}}
                    <div class="px-4">
                        <input type="text" name="type" value="{{ request('type') }}"
                            class="form-control form-control-sm rounded-pill border-0 shadow-none"
                            placeholder="Search by Lot ID or Title">
                    </div>

                    {{-- Status with extra padding --}}
                    <div class="px-4">
                        <select name="status" id="status"
                            class="form-select form-select-sm rounded-pill border-0 shadow-none">
                            <option value="">All Status</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Live</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Sold</option>
                        </select>
                    </div>

                    {{-- Categories with extra padding --}}
                    <div class="px-4">
                        <select name="category_id" class="form-select form-select-sm rounded-pill border-0 shadow-none">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Weight --}}
                    <div class="px-2">
                        <input type="text" name="weight" value="{{ request('weight') }}"
                            class="form-control form-control-sm rounded-pill border-0 shadow-none" placeholder="Weight">
                    </div>

                    {{-- Filter Button --}}
                    <div class="px-2">
                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                            Filter
                        </button>
                    </div>

                    {{-- Reset Button --}}
                    <div class="px-2">
                        <a href="{{ route('admin.lots.index') }}"
                            class="btn btn-sm btn-link text-decoration-none text-muted px-3">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-4">
                            <h4 class="card-title mb-0 fw-semibold">All Lots</h4>
                            <a href="{{ route('admin.lots.create') }}" class="btn btn-success btn-sm fw-semibold shadow-sm"
                                title="Add Lot">
                                + Add
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success m-4 rounded-3 shadow-sm" id="success-alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger m-4 rounded-3 shadow-sm" id="success-alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="card-body py-4 mt-1">
                            <div class="table-responsive">
                                <table id="lotsTable"
                                    class="table table-hover align-middle text-nowrap table-bordered rounded-3 overflow-hidden">
                                    <thead class="table-light align-middle">
                                        <tr>
                                            {{-- <th>SL</th> --}}
                                            <th>Lot ID</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Weight</th>
                                            <th>Seller</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lots as $lot)
                                            <tr>
                                                {{-- <td>{{ $loop->iteration }}</td> --}}
                                                <td><b>{{ $lot->id }}</b></td>
                                                <td>{{ $lot->title }}</td>
                                                <td>{{ $lot->type }}</td>
                                                <td>{{ $lot->weight }}</td>
                                                <td>{{ $lot->seller ? $lot->seller->full_name : 'N/A' }}</td>
                                                <td>
                                                    @if ($lot->status == 0)
                                                        <span
                                                            class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1 small">Pending</span>
                                                    @elseif($lot->status == 1)
                                                        <span
                                                            class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1 small">Live</span>
                                                    @else
                                                        <span
                                                            class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-1 small">Sold</span>
                                                    @endif
                                                </td>
                                                <td class="text-start">
                                                    <a href="{{ route('admin.lots.show', $lot->id) }}"
                                                        class="text-decoration-none text-primary me-3 d-inline-flex align-items-center"
                                                        title="View">
                                                        <i class="bi bi-eye me-1"></i> <span>View</span>
                                                    </a>

                                                    <a href="{{ route('admin.lots.edit', $lot->id) }}"
                                                        class="text-decoration-none text-secondary me-3 d-inline-flex align-items-center"
                                                        title="Edit">
                                                        <i class="bi bi-pencil-square me-1"></i> <span>Edit</span>
                                                    </a>

                                                    <form action="{{ route('admin.lots.destroy', $lot->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="btn btn-link text-danger text-decoration-none p-0 d-inline-flex align-items-center"
                                                            onclick="return confirm('Are you sure to delete this?')"
                                                            title="Delete">
                                                            <i class="bi bi-trash me-1"></i> <span>Delete</span>
                                                        </button>
                                                    </form>
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
                    setTimeout(() => alert.remove(), 500); // remove after fade out
                }, 3000); // 3 seconds
            }
        };
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#lotsTable').DataTable({
                lengthChange: false,
                searching: true,
                ordering: false
                // 'columnDefs': [{
                //     'targets': [4], // column index (start from 0)
                //     'orderable': false, // set orderable false for selected columns
                // }]
            });
        });
    </script>
@endpush

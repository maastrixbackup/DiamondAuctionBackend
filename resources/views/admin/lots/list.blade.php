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
                            <a href="#">Lots</a>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <!--<form method="GET" action="{{ route('admin.lots.index') }}" class="mb-3">-->
                    <!--    <div class="row g-3 align-items-end">-->
                    <!--        <div class="col-md-3">-->
                    <!--            <label for="status" class="form-label">Status</label>-->
                    <!--            <select name="status" id="status" class="form-control">-->
                    <!--                <option value="">-- All --</option>-->
                    <!--                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>-->
                    <!--                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>-->
                    <!--                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Sold</option>-->
                    <!--            </select>-->
                    <!--        </div>-->

                    <!--        <div class="col-md-3">-->
                    <!--            <label for="type" class="form-label">Type</label>-->
                    <!--            <input type="text" name="type" id="type" value="{{ request('type') }}"-->
                    <!--                class="form-control" placeholder="Search Type">-->
                    <!--        </div>-->

                    <!--        <div class="col-md-3">-->
                    <!--            <label for="weight" class="form-label">Weight</label>-->
                    <!--            <input type="text" name="weight" id="weight" value="{{ request('weight') }}"-->
                    <!--                class="form-control" placeholder="Search Weight">-->
                    <!--        </div>-->

                    <!--        <div class="col-md-3 d-flex gap-2">-->
                    <!--            <button type="submit" class="btn btn-primary w-100">Search</button>-->
                    <!--            <a href="{{ route('admin.lots.index') }}" class="btn btn-secondary w-100">Reset</a>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</form>-->
                    <form method="GET" action="{{ route('admin.lots.index') }}" class="mb-4">
                        <div class="bg-light rounded-4 px-3 py-4 d-flex flex-wrap align-items-center gap-2 shadow-sm">

                            {{-- Search Field with extra padding --}}
                            <div class="px-4">
                                <input type="text" name="type" value="{{ request('type') }}"
                                    class="form-control form-control-sm rounded-pill border-0 shadow-none"
                                    placeholder="Search by Lot ID, Title, or Seller">
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
                                <select name="category_id"
                                    class="form-select form-select-sm rounded-pill border-0 shadow-none">
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
                                    class="form-control form-control-sm rounded-pill border-0 shadow-none"
                                    placeholder="Weight">
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


                    <!--<div class="card">-->
                    <!--    <div class="card-header d-flex justify-content-between align-items-center">-->
                    <!--        <h4 class="card-title mb-0">All Lots</h4>-->
                    <!--        <a href="{{ route('admin.lots.create') }}" class="btn btn-primary" title="Add Lot">-->
                    <!--            + Add-->
                    <!--        </a>-->
                    <!--    </div>-->
                    <!--    @if (session('success'))
    -->
                    <!--        <div class="alert alert-success" id="success-alert">-->
                    <!--            {{ session('success') }}-->
                    <!--        </div>-->
                    <!--
    @endif-->

                    <!--    <div class="card-body">-->
                    <!--        <div class="table-responsive">-->
                    <!--            <table id="basic-datatables" class="display table table-striped table-hover">-->
                    <!--                <thead>-->
                    <!--                    <tr>-->
                    <!--                        <th>SL</th>-->
                    <!--                        <th>Seller</th>-->
                    <!--                        <th>Type</th>-->
                    <!--                        <th>Weight</th>-->
                    <!--                        <th>Status</th>-->
                    <!--                        <th>Action</th>-->
                    <!--                    </tr>-->
                    <!--                </thead>-->
                    <!--                <tbody>-->
                    <!--                    @foreach ($lots as $lot)
    -->
                    <!--                        <tr>-->
                    <!--                            <td>{{ $loop->iteration }}</td>-->
                    <!--                            {{-- <td>{{ $lot->seller->full_name }}</td> --}}-->
                    <!--                            <td>{{ $lot->seller ? $lot->seller->full_name : 'N/A' }}</td>-->
                    <!--                            <td>{{ $lot->type }}</td>-->
                    <!--                            <td>{{ $lot->weight }}</td>-->
                    <!--                            <td>-->
                    <!--                                @if ($lot->status == 0)
    -->
                    <!--                                    <span class="badge bg-danger">Pending</span>-->
                    <!--
@elseif($lot->status == 1)
    -->
                    <!--                                    <span class="badge bg-success">Active</span>-->
                <!--                                @else-->
                    <!--                                    <span class="badge bg-secondary">Sold</span>-->
                    <!--
    @endif-->
                    <!--                            </td>-->
                    <!--                            <td>-->
                    <!--                                <a href="{{ route('admin.lots.edit', $lot->id) }}"-->
                    <!--                                    class="btn btn-sm btn-info" title="Edit"><i-->
                    <!--                                        class="icon-pencil"></i></a>-->
                    <!--                                <a href="{{ route('admin.lots.show', $lot->id) }}"-->
                    <!--                                    class="btn btn-sm btn-primary" title="View">-->
                    <!--                                    <i class="icon-eye"></i>-->
                    <!--                                </a>-->
                    <!--                                <form action="{{ route('admin.lots.destroy', $lot->id) }}"-->
                    <!--                                    method="POST" class="d-inline">-->
                    <!--                                    @csrf-->
                    <!--                                    @method('DELETE')-->
                    <!--                                    <button class="btn btn-sm btn-danger" title="Delete"-->
                    <!--                                        onclick="return confirm('Are you sure to delete this?')"><i-->
                    <!--                                            class="icon-trash"></i></button>-->
                    <!--                                </form>-->
                    <!--                            </td>-->
                    <!--                        </tr>-->
                    <!--
    @endforeach-->
                    <!--                </tbody>-->
                    <!--            </table>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-4">
                            <h4 class="card-title mb-0 fw-semibold">All Lots</h4>
                            <a href="{{ route('admin.lots.create') }}"
                                class="btn btn-success btn-sm fw-semibold shadow-sm" title="Add Lot">
                                + Add
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success m-4 rounded-3 shadow-sm" id="success-alert">
                                {{ session('success') }}
                            </div>
                            {{-- <script>
                                // Check if the page has already been reloaded
                                if (!sessionStorage.getItem('reloaded')) {
                                    sessionStorage.setItem('reloaded', 'true');
                                    location.reload();
                                } else {
                                    // Clear the flag after reload
                                    sessionStorage.removeItem('reloaded');
                                }
                            </script> --}}
                        @endif

                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table id="lotsTable"
                                    class="table table-hover align-middle text-nowrap table-bordered rounded-3 overflow-hidden">
                                    <thead class="table-light align-middle">
                                        <tr>
                                            <th>SL</th>
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
                                                <td>{{ $loop->iteration }}</td>
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
                searching: false,
                ordering: false
                // 'columnDefs': [{
                //     'targets': [4], // column index (start from 0)
                //     'orderable': false, // set orderable false for selected columns
                // }]
            });
        });
    </script>
@endpush

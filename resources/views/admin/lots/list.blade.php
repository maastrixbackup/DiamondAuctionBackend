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
                    <form method="GET" action="{{ route('admin.lots.index') }}" class="mb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- All --</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Sold</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="type" class="form-label">Type</label>
                                <input type="text" name="type" id="type" value="{{ request('type') }}"
                                    class="form-control" placeholder="Search Type">
                            </div>

                            <div class="col-md-3">
                                <label for="weight" class="form-label">Weight</label>
                                <input type="text" name="weight" id="weight" value="{{ request('weight') }}"
                                    class="form-control" placeholder="Search Weight">
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                                <a href="{{ route('admin.lots.index') }}" class="btn btn-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">All Lots</h4>
                            <a href="{{ route('admin.lots.create') }}" class="btn btn-primary" title="Add Lot">
                                + Add
                            </a>
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
                                            <th>Seller</th>
                                            <th>Type</th>
                                            <th>Weight</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lots as $lot)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- <td>{{ $lot->seller->full_name }}</td> --}}
                                                <td>{{ $lot->seller ? $lot->seller->full_name : 'N/A' }}</td>
                                                <td>{{ $lot->type }}</td>
                                                <td>{{ $lot->weight }}</td>
                                                <td>
                                                    @if ($lot->status == 0)
                                                        <span class="badge bg-danger">Pending</span>
                                                    @elseif($lot->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Sold</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.lots.edit', $lot->id) }}"
                                                        class="btn btn-sm btn-info" title="Edit"><i
                                                            class="icon-pencil"></i></a>
                                                    <a href="{{ route('admin.lots.show', $lot->id) }}"
                                                        class="btn btn-sm btn-primary" title="View">
                                                        <i class="icon-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.lots.destroy', $lot->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger" title="Delete"
                                                            onclick="return confirm('Are you sure to delete this?')"><i
                                                                class="icon-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No data found.</td>
                                            </tr>
                                        @endforelse
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
@endpush

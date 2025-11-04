@extends('admin.layouts.app')
@section('title', 'Category List')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">Categories</h3>
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
                            <a href="javascript:;">Categories</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light py-3 px-4">
                <h4 class="card-title mb-0 fw-semibold">All Categories</h4>
                <a href="{{ route('admin.category.create') }}" class="btn btn-sm btn-primary">
                    + Add
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success m-4" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="basic-datatables" class="table table-bordered table-striped table-hover align-middle text-sm">
                        <thead class="table-light">
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.category.edit', $category->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.category.destroy', $category->id) }}"
                                                  method="POST" onsubmit="return confirm('Are you sure to delete this?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="icon-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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

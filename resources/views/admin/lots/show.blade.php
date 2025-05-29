@extends('admin.layouts.app')
@section('title', 'View Lot')

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
                            <a href="{{ route('admin.lots.index') }}">Lots</a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">View Lot</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lot Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>ID</th>
                                <td>{{ $lot->id }}</td>
                            </tr>
                            <tr>
                                <th>Seller</th>
                                <td>{{ $lot->seller ? $lot->seller->full_name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $lot->category ? $lot->category->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Type</th>
                                <td>{{ $lot->type }}</td>
                            </tr>
                            <tr>
                                <th>Color</th>
                                <td>{{ $lot->color }}</td>
                            </tr>
                            <tr>
                                <th>Weight</th>
                                <td>{{ $lot->weight }}</td>
                            </tr>
                            <tr>
                                <th>Size</th>
                                <td>{{ $lot->size }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($lot->status == 0)
                                        <span class="badge bg-danger">Pending</span>
                                    @elseif ($lot->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Sold</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Shape</th>
                                <td>{{ $lot->shape }}</td>
                            </tr>
                            <tr>
                                <th>Batch Code</th>
                                <td>{{ $lot->batch_code }}</td>
                            </tr>
                            <tr>
                                <th>Report Number</th>
                                <td>{{ $lot->report_number }}</td>
                            </tr>
                            <tr>
                                <th>Colour Grade</th>
                                <td>{{ $lot->colour_grade }}</td>
                            </tr>
                            <tr>
                                <th>Colour Origin</th>
                                <td>{{ $lot->colour_origin }}</td>
                            </tr>
                            <tr>
                                <th>Colour Distribution</th>
                                <td>{{ $lot->colour_distribution }}</td>
                            </tr>
                            <tr>
                                <th>Polish</th>
                                <td>{{ $lot->polish }}</td>
                            </tr>
                            <tr>
                                <th>Symmetry</th>
                                <td>{{ $lot->symmetry }}</td>
                            </tr>
                            <tr>
                                <th>Fluorescence</th>
                                <td>{{ $lot->fluorescence }}</td>
                            </tr>
                            <tr>
                                <th>Stone</th>
                                <td>{{ $lot->stone }}</td>
                            </tr>
                            <tr>
                                <th>Notes</th>
                                <td>{!! nl2br(e($lot->notes)) !!}</td>
                            </tr>
                            <tr>
                                <th>Images</th>
                                <td>
                                    @if ($lot->images && is_array($lot->images) && count($lot->images))
                                        <div class="d-flex flex-wrap">
                                            @foreach ($lot->images as $image)
                                                <div>
                                                    <img src="{{ asset('storage/images/lots/' . $image) }}"
                                                        style="max-width: 100px; max-height: 100px;" class="rounded p-1">
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">No images uploaded</p>
                                    @endif

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('admin.lots.index') }}" class="btn btn-secondary mt-3">Back to List</a>
                </div>
            </div>
        </div>
    </div>
@endsection

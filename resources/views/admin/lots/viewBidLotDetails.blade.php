@extends('admin.layouts.app')
@section('title', 'View Details')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0"> Details</h3>
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
                            <a href="{{ route('admin.bid-details') }}">Bids</a>
                        </li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item">Bid Details</li>
                    </ul>
                </div>
            </div>

            <div class="row g-4 mb-3">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        @if ($lot->images && is_array($lot->images) && count($lot->images))
                            <!-- Main image carousel -->
                            <div id="lotImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach ($lot->images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/images/lots/' . $image) }}"
                                                class="d-block w-100 img-fluid rounded-top"
                                                style="object-fit: cover; max-height: 400px;">
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Carousel controls -->
                                @if (count($lot->images) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#lotImageCarousel"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#lotImageCarousel"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="p-5 text-center text-muted">No image available</div>
                        @endif

                        <div
                            class="card-body border-top d-flex flex-wrap justify-content-around text-secondary small fw-semibold">
                            <div><i class="fa fa-ruler-combined me-1 text-primary"></i>Size: {{ $lot->size ?? '-' }}</div>
                            <div><i class="fa fa-gem me-1 text-primary"></i>Stones: {{ $lot->stone ?? '-' }}</div>
                            <div><i class="fa fa-weight-hanging me-1 text-primary"></i>Weight: {{ $lot->weight ?? '-' }}
                            </div>
                            <div><i class="fa fa-shapes me-1 text-primary"></i>Shape: {{ $lot->shape ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mt-3">
                        <div class="card-body ">
                            <h5 class="card-title border-bottom pb-2 mb-3 text-primary">
                                <i class="fas fa-file-alt me-2"></i>Bid Details
                            </h5>
                            <div class="table-responsive">
                                <table
                                    class="table table-hover align-middle text-nowrap table-bordered rounded-3 overflow-hidden">
                                    <thead>
                                        <tr>
                                            <th>Sl.No</th>
                                            <th>Lot ID</th>
                                            <th>Bidder</th>
                                            <th>Price</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($bb))
                                            @foreach ($bb as $b)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $b['lot_id'] }}</td>
                                                    <td>{{ $b['bidder_name'] }}</td>
                                                    <td>{{ $b['price'] }}</td>
                                                    <td>{{ $b['bidding_time'] }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">No Data Found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Lot Info -->
                <div class="col-lg-6">
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3 text-primary">
                                <i class="fas fa-gem me-2"></i>Lot Details
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Lot ID:</strong> {{ $lot->id }}</div>
                                        <div><strong>Seller:</strong> {{ $lot->seller?->full_name ?? 'N/A' }}</div>
                                        <div><strong>Category:</strong> {{ $lot->category?->name ?? 'N/A' }}</div>
                                        <div><strong>Type:</strong> {{ $lot->type }}</div>
                                        <div><strong>Color:</strong> {{ $lot->color }}</div>
                                        <div><strong>Status:</strong>
                                            @if ($lot->status == 0)
                                                <span class="badge bg-danger">Pending</span>
                                            @elseif ($lot->status == 1)
                                                <span class="badge bg-success">Live</span>
                                            @else
                                                <span class="badge bg-secondary">Sold</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Weight:</strong> {{ $lot->weight }}</div>
                                        <div><strong>Size:</strong> {{ $lot->size }}</div>
                                        <div><strong>Stone:</strong> {{ $lot->stone }}</div>
                                        <div><strong>Shape:</strong> {{ $lot->shape }}</div>
                                        <div><strong>Clarity:</strong> {{ $lot->clarity }}</div>
                                        <div><strong>Batch Code:</strong> {{ $lot->batch_code }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <strong>Notes:</strong>
                                <div class="bg-light rounded-3 p-3 mt-1">
                                    {!! nl2br(e($lot->notes)) !!}
                                </div>
                            </div>
                            <div class="mt-1">
                                <strong>Description:</strong>
                                <div class="bg-light rounded-3 p-3 mt-1">
                                    {{-- {!! nl2br(e($lot->description)) !!} --}}
                                    {!! $lot->description ? nl2br(e($lot->description)) : 'N/A' !!}
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3 text-primary">
                                <i class="fas fa-file-alt me-2"></i>Report & Quality Details
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Report Number:</strong> {{ $lot->report_number }}</div>
                                        <div><strong>Colour Grade:</strong> {{ $lot->colour_grade }}</div>
                                        <div><strong>Origin:</strong> {{ $lot->colour_origin }}</div>
                                        <div><strong>Distribution:</strong> {{ $lot->colour_distribution }}</div>
                                        <div>
                                            <strong>Report Document:</strong>
                                            @if ($lot->report_document)
                                                <a href="{{ asset('storage/document/lots/' . $lot->report_document) }}"
                                                    target="_blank" class="btn btn-sm btn-primary">View</a>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div><strong>Polish:</strong> {{ $lot->polish }}</div>
                                        <div><strong>Symmetry:</strong> {{ $lot->symmetry }}</div>
                                        <div><strong>Fluorescence:</strong> {{ $lot->fluorescence }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <a href="{{ route('admin.bid-details') }}" class="btn btn-secondary mt-4">Back</a>
                </div>
            </div>

        </div>
    </div>

@endsection

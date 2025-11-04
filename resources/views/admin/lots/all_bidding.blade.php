@extends('admin.layouts.app')
@section('title', 'Bids List')

@section('content')
    <div class="row mt-lg-4 pt-lg-4">
        <div class="page-inner">
            <div class="page-header d-flex align-items-center justify-content-between flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h3 class="fw-bold mb-0">All Bids</h3>
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
                            <a href="javascript:;">All Bids</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3 px-4">
                            <h4 class="card-title mb-0 fw-semibold">All Bids</h4>
                            <div class="d-flex gap-2">
                                {{-- <a href="{{ route('admin.lotsExport', request()->query()) }}" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm"
                                    title="Export CSV">
                                    <i class="bi bi-download me-1"></i> Export CSV
                                </a>
                                <a href="{{ route('admin.lots.create') }}"
                                    class="btn btn-success btn-sm fw-semibold shadow-sm" title="Add Lot">
                                    + Add
                                </a> --}}
                            </div>
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
                                            <th>Lot ID</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Weight</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestBids as $bid)
                                            @php
                                                $lotData = \App\Models\Lot::find($bid->lot_id);
                                                $bData = \App\Models\Bidder::find($bid->bidder_id);
                                            @endphp
                                            <tr>
                                                <td><b>{{ $bid->lot_id }}</b></td>
                                                <td>
                                                    @if ($lotData && $lotData->images && is_array($lotData->images))
                                                        <img src="{{ asset('storage/images/lots/' . $lotData->images[0]) }}"
                                                            width="100px" height="auto" class="rounded" alt="Diamond">
                                                    @else
                                                        <img src="{{ asset('storage/images/lots/sample.jpg') }}"
                                                            width="100px" height="auto" class="rounded" alt="Diamond">
                                                    @endif
                                                </td>
                                                <td>{{ $lotData->title ?? 'N/A' }}</td>
                                                <td>{{ $lotData->weight ?? 'N/A' }}</td>
                                                <td>
                                                    <b>
                                                        ${{ $bid->max_price }}
                                                    </b>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.viewLotBidDetails', $bid->lot_id) }}"
                                                        onclick="viewBidDetails('{{ $bid->lot_id }}','{{ $loop->iteration }}')"
                                                        class="text-decoration-none text-primary me-3 d-inline-flex align-items-center"
                                                        title="View details">
                                                        <i class="bi bi-eye me-1"></i> <span>View</span>
                                                    </a>
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

    <div class="modal fade" id="lotsModal" tabindex="-1" aria-labelledby="lotsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="lotsModalLabel">Bid Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="lotsModalBody">
                    {{-- Loading... --}}
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



        const viewLots = (lotId, k) => {
            const params = new URLSearchParams({
                lot_id: lotId,
            });

            const fullUrl = "{{ url('/admin/viewLotBidDetails') }}" + '?' + params.toString();

            fetch(fullUrl)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('lotsModalBody').innerHTML = data;
                    new bootstrap.Modal(document.getElementById('lotsModal')).show();
                })
                .catch(error => {
                    document.getElementById('lotsModalBody').innerHTML = 'Error loading lots.';
                    console.error(error);
                });
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

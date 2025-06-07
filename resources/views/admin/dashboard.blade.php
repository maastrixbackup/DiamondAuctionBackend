@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')

    <section class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card text-white bg-primary shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Sellers</h6>
                        <h2 class="fw-bold">{{ $totalSellers }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Bidders</h6>
                        <h2 class="fw-bold">{{ $totalBidders }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Lots</h6>
                        <h2 class="fw-bold">{{ $totalLots }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title">Pending Slot Requests</h6>
                        <h2 class="fw-bold">{{ $pendingSlotRequests }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">🧑‍💼 Recent Sellers</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($recentSellers as $seller)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $seller->full_name }}</strong><br><small
                                            class="text-muted">{{ $seller->type == 1 ? 'Company' : ($seller->type == 2 ? 'Individual' : 'N/A') }}</small>
                                    </div>
                                    <span
                                        class="badge bg-secondary">{{ \Carbon\Carbon::parse($seller->created_at)->format('d M Y') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">
                                    No recent sellers found.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-success">📅 Recent Slot Bookings</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($recentSlotBookings as $booking)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $booking->bidder_name }}</strong><br><small
                                            class="text-muted">{{ $booking->room_name }}</small>
                                    </div>
                                    <span
                                        class="badge bg-outline-dark">{{ \Carbon\Carbon::parse($booking->date_for_reservation . ' ' . $booking->start_time)->format('d M, h:i A') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-dark">👥 Recent Bidders</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($recentBidders as $bidder)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $bidder->full_name }}</strong><br><small
                                            class="text-muted">{{ $bidder->type == 1 ? 'Company' : ($bidder->type == 2 ? 'Individual' : 'N/A') }}</small>
                                    </div>
                                    <span
                                        class="badge bg-secondary">{{ \Carbon\Carbon::parse($bidder->created_at)->format('d M Y') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">
                                    No recent sellers found.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-danger">💰 Recent Bids</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>LOT-1007</strong><br><small class="text-muted">Neha Patel</small>
                                </div>
                                <span class="badge bg-success">₹ 12,50,000</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>LOT-1005</strong><br><small class="text-muted">Rohit Sen</small>
                                </div>
                                <span class="badge bg-success">₹ 9,80,000</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>LOT-1003</strong><br><small class="text-muted">Ajay Mehra</small>
                                </div>
                                <span class="badge bg-success">₹ 7,60,000</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

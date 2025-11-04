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
                                        <strong class="text-capitalize">{{ $seller->full_name }}</strong><br><small
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
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-capitalize">{{ $booking->bidder_name }}</strong><br>
                                        <small class="text-muted text-capitalize">{{ $booking->room_name }}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            📅 {{ \Carbon\Carbon::parse($booking->date_for_reservation)->format('d M Y') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            🕒 {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}
                                        </small>
                                    </div>
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
                                        <strong class="text-capitalize">{{ $bidder->full_name }}</strong><br><small
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
                        <table class="table">
                            <thead>
                                @forelse($recentBids as $bid)
                                    @php
                                        $bData = \App\Models\Bidder::find($bid->bidder_id);
                                    @endphp
                                    <tr class="text-capitalize">
                                        <th>LOT-{{ $bid->lot_id }}</th>
                                        <td><span
                                                title="{{ $bid->lot->title ?? '' }}">{{ strlen($bid->lot->title ?? '') > 20 ? substr($bid->lot->title ?? '', 0, 20) . '...' : $bid->lot->title ?? 'N/A' }}</span>
                                        </td>
                                        {{-- <td><span
                                                title="{{ $bid->lot->title }}">{{ strlen($bid->lot->title) > 20 ? substr($bid->lot->title, 0, 20) . '...' : $bid->lot->title ?? 'N/A' }}</span>
                                        </td> --}}
                                        <td>{{ $bData->full_name }}</td>
                                        <td> <span class="badge bg-success">$
                                                {{ number_format($bid->price, 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No recent bids available.</td>
                                    </tr>
                                @endforelse
                            </thead>
                        </table>
                        <!--<ul class="list-group list-group-flush">-->
                        <!--    @forelse($recentBids as $bid)
    -->
                        <!--        <li class="list-group-item d-flex justify-content-between align-items-center">-->
                        <!--            <div>-->
                        <!--                <strong>LOT-{{ $bid->lot_id }}</strong><br><small-->
                        <!--                    class="text-muted">{{ $bid->bidder_name }}</small>-->
                        <!--            </div>-->
                        <!--            <span class="badge bg-success">$ {{ number_format($bid->bidding_price, 2) }}</span>-->
                        <!--        </li>-->
                    <!--    @empty-->
                        <!--        <li class="list-group-item text-muted">No recent bids available.</li>-->
                        <!--
    @endforelse-->
                        <!--</ul>-->
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

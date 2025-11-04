<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use App\Models\BulkBidding;
use App\Models\Lot;
use App\Models\Seller;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $totalSellers = Seller::where('account_status', 1)->count();
        $totalBidders = Bidder::where('account_status', 1)->count();
        $totalLots = Lot::where('status', 1)->count();
        $pendingSlotRequests = SlotBooking::where('status', 0)->count();
        $recentSellers = Seller::where('account_status', 1)
            ->select('full_name', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $recentBidders = Bidder::where('account_status', 1)
            ->select('full_name', 'type', 'created_at')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        $latestBookingIds = SlotBooking::where('status', 1)
            ->selectRaw('MAX(id) as id')
            ->groupBy('booking_id')
            ->orderByDesc('id')
            ->take(3)
            ->pluck('id');
        $recentSlotBookings = SlotBooking::whereIn('id', $latestBookingIds)
            ->orderByDesc('id')
            ->get(['bidder_name', 'room_name', 'room_type', 'start_time', 'date_for_reservation']);

        // $recentBids = SlotBooking::whereNotNull('bidding_price')
        //     ->orderByDesc('date_for_reservation')
        //     ->take(3)
        //     ->get(['lot_id', 'bidder_name', 'bidding_price']);

        // B
        $recentBids = BulkBidding::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('bulk_biddings')
                ->whereNotNull('price')
                ->groupBy('lot_id', 'bidder_id');
        })
            ->with('lot')
            ->orderByDesc('id')
            ->take(5)
            ->get();



        return view('admin.dashboard', compact(
            'totalSellers',
            'totalBidders',
            'totalLots',
            'pendingSlotRequests',
            'recentSellers',
            'recentBidders',
            'recentSlotBookings',
            'recentBids'
        ));
    }

    public function profileDetails()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

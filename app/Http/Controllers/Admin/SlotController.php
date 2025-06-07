<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currD = date('Y-m-d');
        // dd($currD);
        $additionalD = date('Y-m-d', strtotime("+6 day"));

        $roomName = $request->room ?? '';
        $reqDay = $request->day ?? '';

        // Step 1: Get grouped rows with MAX(status)
        $subQuery = DB::table('slot_bookings')
            ->select(
                DB::raw('MAX(status) as max_status'),
                'room_name',
                'start_time',
                'date_for_reservation'
            )
            ->where('status', 1)
            // ->whereIn('status', [1, 2])
            ->whereBetween('date_for_reservation', [$currD, $additionalD])
            ->groupBy('room_name', 'start_time', 'date_for_reservation');

        if ($roomName) {
            $subQuery->where('room_name', $roomName);
        }

        if ($reqDay) {
            $subQuery->where('date_for_reservation', $reqDay);
        }

        // Step 2: Join to fetch complete row with lot_id, bidder_name, etc.
        $slotBooking = DB::table('slot_bookings as sb')
            ->joinSub($subQuery, 'latest', function ($join) {
                $join->on('sb.room_name', '=', 'latest.room_name')
                    ->on('sb.start_time', '=', 'latest.start_time')
                    ->on('sb.date_for_reservation', '=', 'latest.date_for_reservation')
                    ->on('sb.status', '=', 'latest.max_status');
            })
            ->select(
                'sb.lot_id',
                'sb.bidder_name',
                'sb.room_name',
                'sb.start_time',
                'sb.date_for_reservation',
                'sb.status'
            )
            ->get();

        $grouped = $slotBooking->groupBy(function ($item) {
            return $item->bidder_name . '|' . $item->room_name . '|' . $item->start_time . '|' . $item->date_for_reservation;
        })->map(function ($group) {
            $first = $group->first();
            return (object) [
                'bidder_name' => $first->bidder_name,
                'room_name' => $first->room_name,
                'start_time' => $first->start_time,
                'date_for_reservation' => $first->date_for_reservation,
                'status' => $first->status,
                'lot_ids' => $group->pluck('lot_id')->all(),
            ];
        })->values();


        // dd($slotBooking, $grouped);
        // $day = $request->has('day') && is_string($request->day) ? $request->day : '';
        return view('admin.slots.list', compact('slotBooking', 'grouped', 'roomName', 'reqDay'));
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

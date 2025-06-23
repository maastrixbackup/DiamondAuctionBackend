<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Lot;
use App\Models\Room;
use App\Models\Seller;
use App\Models\Slot;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $query = Lot::with('seller');
        $query = Lot::with(['seller', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->type . '%')
                    ->orWhere('id', $request->type);
            });
        }

        if ($request->filled('weight')) {
            $query->where('weight', 'like', '%' . $request->weight . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $lots = $query->orderBy('id', 'desc')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.lots.list', compact('lots', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sellers = Seller::select('id', 'full_name')->where('account_status', 1)->get();
        $categories = Category::latest()->get();
        return view('admin.lots.add', compact('sellers', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seller_id' => 'required|exists:sellers,id',
            'category_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'weight' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'stone' => 'nullable|string|max:255',
            'shape' => 'nullable|string|max:255',
            'clarity' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'batch_code' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
            'report_number' => 'nullable|string|max:255',
            'report_document' => 'nullable|mimes:pdf|max:5120',
            'colour_grade' => 'nullable|string|max:255',
            'colour_origin' => 'nullable|string|max:255',
            'colour_distribution' => 'nullable|string|max:255',
            'polish' => 'nullable|string|max:255',
            'symmetry' => 'nullable|string|max:255',
            'fluorescence' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $lot = new Lot();
            $lot->seller_id = $request->seller_id;
            $lot->category_id = $request->category_id;
            $lot->title = $request->title;
            $lot->description = $request->description;
            $lot->video = $request->video;
            $lot->type = $request->type;
            $lot->color = $request->color;
            $lot->weight = $request->weight;
            $lot->size = $request->size;
            $lot->stone = $request->stone;
            $lot->shape = $request->shape;
            $lot->clarity = $request->clarity;
            $lot->notes = $request->notes;
            $lot->batch_code = $request->batch_code;
            $lot->status = $request->status;
            $lot->report_number = $request->report_number;
            $lot->colour_grade = $request->colour_grade;
            $lot->colour_origin = $request->colour_origin;
            $lot->colour_distribution = $request->colour_distribution;
            $lot->polish = $request->polish;
            $lot->symmetry = $request->symmetry;
            $lot->fluorescence = $request->fluorescence;

            // Handle multiple image uploads
            $imageNames = [];
            $destinationPath = public_path('storage/images/lots/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageName = 'lot_image_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    if ($image->move($destinationPath, $imageName)) {
                        $imageNames[] = $imageName;
                    } else {
                        return redirect()->back()->with('error', 'Failed to upload one of the images.');
                    }
                }
            }

            // $lot->images = json_encode($imageNames);
            $lot->images = $imageNames;

            $reportDocPath = public_path('storage/document/lots/');
            if (!is_dir($reportDocPath)) {
                mkdir($reportDocPath, 0777, true);
            }

            if ($request->hasFile('report_document')) {
                $file = $request->file('report_document');
                $filename = 'report_document_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($reportDocPath, $filename);
                $lot->report_document = $filename;
            }

            $lot->save();

            return redirect()->route('admin.lots.index')->with('success', 'Lot created successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $lot = Lot::with('seller')->findOrFail($id);
        $lot = Lot::with(['seller', 'category'])->findOrFail($id);
        return view('admin.lots.show', compact('lot'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lot = Lot::findOrFail($id);
        $sellers = Seller::where('account_status', 1)->get();
        $categories = Category::latest()->get();
        return view('admin.lots.edit', compact('lot', 'sellers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'seller_id' => 'required|exists:sellers,id',
            'category_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'weight' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'stone' => 'nullable|string|max:255',
            'shape' => 'nullable|string|max:255',
            'clarity' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'batch_code' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
            'report_number' => 'nullable|string|max:255',
            'report_document' => 'nullable|mimes:pdf|max:5120',
            'colour_grade' => 'nullable|string|max:255',
            'colour_origin' => 'nullable|string|max:255',
            'colour_distribution' => 'nullable|string|max:255',
            'polish' => 'nullable|string|max:255',
            'symmetry' => 'nullable|string|max:255',
            'fluorescence' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        try {
            $lot = Lot::findOrFail($id);
            $lot->seller_id = $request->seller_id;
            $lot->category_id = $request->category_id;
            $lot->title = $request->title;
            $lot->description = $request->description;
            $lot->video = $request->video;
            $lot->type = $request->type;
            $lot->color = $request->color;
            $lot->weight = $request->weight;
            $lot->size = $request->size;
            $lot->stone = $request->stone;
            $lot->shape = $request->shape;
            $lot->clarity = $request->clarity;
            $lot->notes = $request->notes;
            $lot->batch_code = $request->batch_code;
            $lot->status = $request->status;
            $lot->report_number = $request->report_number;
            $lot->colour_grade = $request->colour_grade;
            $lot->colour_origin = $request->colour_origin;
            $lot->colour_distribution = $request->colour_distribution;
            $lot->polish = $request->polish;
            $lot->symmetry = $request->symmetry;
            $lot->fluorescence = $request->fluorescence;

            $destinationPath = public_path('storage/images/lots/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $existingImages = $request->input('existing_images', []);
            $storedImages = $lot->images ?? [];
            $newImages = [];

            // Handle new uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image) {
                        // $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $imageName = 'lot_image_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $image->move($destinationPath, $imageName);
                        $newImages[] = $imageName;
                    }
                }
            }

            // Delete removed images from disk
            $removedImages = array_diff($storedImages, $existingImages);
            foreach ($removedImages as $img) {
                $imgPath = $destinationPath . $img;
                if (file_exists($imgPath)) {
                    unlink($imgPath);
                }
            }

            $lot->images = array_values(array_merge($existingImages, $newImages));

            $documentPath = public_path('storage/document/lots/');
            if (!file_exists($documentPath)) {
                mkdir($documentPath, 0777, true);
            }

            if ($request->hasFile('report_document')) {
                if (!empty($lot->report_document)) {
                    $oldDoc = $documentPath . $lot->report_document;
                    if (file_exists($oldDoc)) {
                        unlink($oldDoc);
                    }
                }

                $docFile = $request->file('report_document');
                $docFilename = 'report_document_' . time() . '.' . $docFile->getClientOriginalExtension();
                $docFile->move($documentPath, $docFilename);
                $lot->report_document = $docFilename;
            }

            $lot->save();

            return redirect()->route('admin.lots.index')->with('success', 'Lot updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Lot update failed', ['error' => $th->getMessage(), 'trace' => $th->getTraceAsString()]);
            return back()->with('error', 'Something went wrong while updating the lot. Please try again.');
        }
    }

    public function export(Request $request)
    {
        $query = Lot::with(['seller', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('weight')) {
            $query->where('weight', 'like', '%' . $request->weight . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $lots = $query->orderBy('id', 'desc')->get();

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

        // CSV Headers
        fputcsv($handle, [
            'ID',
            'Seller',
            'Category',
            'Title',
            'Description',
            'Type',
            'Color',
            'Weight',
            'Size',
            'Stone',
            'Shape',
            'Clarity',
            'Notes',
            'Batch Code',
            'Status',
            'Report Number',
            'Report Document',
            'Colour Grade',
            'Origin',
            'Distribution',
            'Polish',
            'Symmetry',
            'Fluorescence',
            'Images',
            'Video',
            'Created At'
        ]);

        foreach ($lots as $lot) {
            fputcsv($handle, [
                $lot->id,
                optional($lot->seller)->full_name ?? 'N/A',
                optional($lot->category)->name ?? 'N/A',
                $lot->title,
                $lot->description,
                $lot->type,
                $lot->color,
                $lot->weight,
                $lot->size,
                $lot->stone,
                $lot->shape,
                $lot->clarity,
                $lot->notes,
                $lot->batch_code,
                match ($lot->status) {
                    0 => 'Pending',
                    1 => 'Live',
                    2 => 'Sold',
                    default => 'Unknown',
                },
                $lot->report_number,
                $lot->report_document,
                $lot->colour_grade,
                $lot->colour_origin,
                $lot->colour_distribution,
                $lot->polish,
                $lot->symmetry,
                $lot->fluorescence,
                is_array($lot->images) ? implode(', ', $lot->images) : $lot->images,
                $lot->video,
                $lot->created_at,
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="lots_export.csv"');
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        $lot = Lot::findOrFail($id);

        // Delete associated images from storage
        if (is_array($lot->images)) {
            foreach ($lot->images as $image) {
                $imagePath = public_path('storage/images/lots/' . $image);
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
        }

        if ($lot->report_document) {
            $documentPath = public_path('storage/document/lots/' . $lot->report_document);
            if (file_exists($documentPath)) {
                @unlink($documentPath);
            }
        }

        $lot->delete();

        return redirect()->route('admin.lots.index')->with('success', 'Lot deleted successfully.');
    }

    public function viewingRequest(Request $request)
    {
        // $groupedSlots = SlotBooking::select('bidder_id', 'room_name', 'bidder_name', 'room_type', 'start_time', 'date_for_reservation')
        //     ->groupBy('bidder_id', 'room_name', 'bidder_name', 'room_type', 'start_time', 'date_for_reservation')
        //     ->get();
        // $groupedSlots = SlotBooking::selectRaw('
        //         bidder_id,
        //         ANY_VALUE(room_name) as room_name,
        //         bidder_name,
        //         room_type,
        //         start_time,
        //         date_for_reservation,
        //         MAX(status) as status
        //     ')
        //     ->groupBy(
        //         'bidder_id',
        //         'bidder_name',
        //         'room_type',
        //         'start_time',
        //         'date_for_reservation'
        //     )
        //     ->get();
        $currDate = date('Y-m-d');
        $groupedSlots = SlotBooking::selectRaw('
                booking_id,
                bidder_id,
                MAX(room_name) as room_name,
                bidder_name,
                room_type,
                start_time,
                date_for_reservation,
                MAX(status) as status
            ')
            ->groupBy(
                'booking_id',
                'bidder_id',
                'bidder_name',
                'room_type',
                'start_time',
                'date_for_reservation'
            )
            // ->where('date_for_reservation', '>=', $currDate)
            // ->orderBy('date_for_reservation', 'desc')
            // ->orderBy('start_time', 'desc')
            ->orderByDesc('booking_id', 'desc')
            ->get();

        return view('admin.lots.viewSlotRequest', compact('groupedSlots'));
    }

    public function viewingRequestLots(Request $request)
    {
        $roomType  = $request->room_type;
        $startTime = $request->start_time;
        $date      = $request->date;
        $bookingId = $request->booking_id;

        $rooms = Room::all();

        $sameTypeRoomIds = $rooms->where('room_type', $roomType)->pluck('id');

        $bookedRoomIds = Slot::whereIn('room_id', $sameTypeRoomIds)
            ->where('date_for_reservation', $date)
            ->where('start_time', $startTime)
            ->where('slot_status', 2)          // 2 = reserved
            ->pluck('room_id')
            ->toArray();

        foreach ($rooms as $room) {
            if ($room->room_type === $roomType) {
                $room->is_available = !in_array($room->id, $bookedRoomIds);
            } else {
                $room->is_available = false;
            }
        }

        $lots = SlotBooking::where('bidder_id', $request->bidder_id)
            ->where('room_type', $roomType)
            ->where('start_time', $startTime)
            ->where('date_for_reservation', $date)
            ->where('status', '!=', 3)
            ->get();

        $isRoomAlreadyAssigned = $lots->contains(fn($lot) => !empty($lot->room_name));

        $requestedLots = [];
        $booking = Booking::where('booking_id', $bookingId)->first();

        if (!empty($booking) && !empty($booking->requested_lot_id)) {
            $lotIds = json_decode($booking->requested_lot_id, true); // Decode JSON to array

            if (is_array($lotIds) && count($lotIds) > 0) {
                $requestedLots = SlotBooking::whereIn('lot_id', $lotIds)
                    ->where('booking_id', $booking->booking_id)
                    ->get();
            }
        }

        $allMeetingLinks = [
            'https://diamondauction.daily.co/1stroom',
            'https://diamondauction.daily.co/2ndroom',
            'https://diamondauction.daily.co/3rdroom',
            'https://diamondauction.daily.co/4throom',
            'https://diamondauction.daily.co/5throom',
        ];

        $usedLinks = SlotBooking::where('start_time', $startTime)
            ->where('date_for_reservation', $date)
            ->whereNotNull('meeting_link')
            ->pluck('meeting_link')
            ->toArray();

        $availableMeetingLinks = array_values(array_diff($allMeetingLinks, $usedLinks));

        return view(
            'admin.lots.viewRequestLots',
            compact('rooms', 'startTime', 'date', 'roomType', 'lots', 'isRoomAlreadyAssigned', 'requestedLots', 'booking', 'availableMeetingLinks')
        );
    }

    public function updateRequestedLotStatus(Request $request)
    {
        $bookingId = $request->booking_id;
        $lotId = (string) $request->lot_id; // ensure it's a string to match existing data
        $status = (int) $request->status;

        DB::beginTransaction();
        try {
            $booking = Booking::where('booking_id', $bookingId)->firstOrFail();

            $slotBooking = SlotBooking::where('lot_id', $lotId)
                ->where('booking_id', $bookingId)
                ->firstOrFail();

            $slotBooking->update(['status' => $status]);

            if ($status === 1) {
                $currentLots = $booking->booking_lot_id ?? [];

                // Avoid duplication
                if (!in_array($lotId, $currentLots)) {
                    $currentLots[] = $lotId;
                    $booking->booking_lot_id = $currentLots;
                    $booking->save();
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $status === 1 ? 'Approved' : 'Rejected',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }


    public function rescheduleBooking(Request $request, $id)
    {
        $reqDay = '';
        $timeFrame = '';
        $roomIds = [];
        $usedLinks = []; // ✅ Ensure this is always defined
        $allMeetingLinks = [
            'https://diamondauction.daily.co/1stroom',
            'https://diamondauction.daily.co/2ndroom',
            'https://diamondauction.daily.co/3rdroom',
            'https://diamondauction.daily.co/4throom',
            'https://diamondauction.daily.co/5throom',
        ];

        if ($request->filled('day') && $request->filled('time')) {
            $reqDay = $request->day;
            $timeFrame = $request->time;
            $roomIds = Slot::where('slot_status', 1)
                ->where('date_for_reservation', $request->day)
                ->where('start_time', $request->time . ':00')
                ->pluck('room_id')
                ->unique()
                ->values()
                ->toArray();

            $usedLinks = SlotBooking::where('date_for_reservation', $reqDay)
                ->where('start_time', $timeFrame)
                ->whereNotNull('meeting_link')
                ->pluck('meeting_link')
                ->toArray();
        }

        $availableMeetingLinks = array_values(array_diff($allMeetingLinks, $usedLinks));

        $booking = Booking::where('booking_id', $id)->firstOrFail();
        // dd($roomIds);
        return view('admin.lots.reschedule', compact('booking', 'reqDay', 'timeFrame', 'roomIds', 'availableMeetingLinks'));
    }

    public function reAssignRoom(Request $request, $id)
    {
        $newDate = $request->re_date;
        $newTime = $request->re_time;
        $newRoom = $request->room;
        $meetingLink = $request->meeting_link;

        DB::beginTransaction();
        try {
            $booking = Booking::findOrFail($id);

            // Fetch the previous room
            $previousRoom = Room::where('room_name', $booking->room_name)->first();
            if ($previousRoom) {
                // Mark previous slot as available
                Slot::where('start_time', $booking->start_time)
                    ->where('date_for_reservation', $booking->date_for_reservation)
                    ->where('room_id', $previousRoom->id)
                    ->update(['slot_status' => 1]);
            }

            // Update booking with new values
            $booking->room_name = $newRoom;
            $booking->start_time = $newTime;
            $booking->date_for_reservation = $newDate;
            $booking->save();

            // Update all related slot bookings
            SlotBooking::where('booking_id', $booking->booking_id)
                ->whereNotIn('status', [3, 2])
                ->each(function ($slotBooking) use ($newRoom, $newTime, $newDate, $meetingLink) {
                    $slotBooking->update([
                        'room_name' => $newRoom,
                        'start_time' => $newTime,
                        'date_for_reservation' => $newDate,
                        'meeting_link' => $meetingLink,
                        'status' => 1
                    ]);
                });

            // Fetch new room and mark its slot as reserved
            $newRoomObj = Room::where('room_name', $newRoom)->first();
            if (!$newRoomObj) {
                throw new \Exception("New room not found.");
            }

            Slot::where('start_time', $newTime)
                ->where('date_for_reservation', $newDate)
                ->where('room_id', $newRoomObj->id)
                ->update(['slot_status' => 2]);

            DB::commit();
            return redirect()->route('admin.viewingRequest')->with('success', 'Rescheduled Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function cancelBidBooking($id)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::where('booking_id', $id)->first();

            if (!$booking) {
                return back()->with('error', 'Booking Not Found');
            }

            if (!empty($booking->room_name)) {
                $room = Room::where('room_name', $booking->room_name)->first();

                if ($room) {
                    Slot::where('room_id', $room->id)
                        ->where('start_time', $booking->start_time)
                        ->where('date_for_reservation', $booking->date_for_reservation)
                        ->update(['slot_status' => 1]);
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Room associated with booking not found.');
                }
            }

            SlotBooking::where('booking_id', $id)->update(['status' => 4]);

            DB::commit();
            return back()->with('success', 'Bidding Canceled Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            // You might want to log the error here as well:
            Log::error('Bid cancellation failed', ['error' => $th]);
            return back()->with('error', 'Something went wrong: ' . $th->getMessage());
        }
    }


    public function assignRoomToSlot(Request $request)
    {
        $request->validate([
            'rooms' => 'required|array',
            'date' => 'required|date',
            'start_time' => 'required',
            'room_type' => 'required|string',
        ]);

        $roomId = $request->rooms[0]; // Take only the first selected room
        $room = Room::find($roomId);

        if (!$room) {
            return response()->json([
                'status' => false,
                'message' => 'Selected room not found.',
            ], 404);
        }
        $meetingLink = $request->meeting_link ?? null;

        $bookingIds = SlotBooking::where('room_type', $request->room_type)
            ->where('start_time', $request->start_time)
            ->where('date_for_reservation', $request->date)
            ->pluck('booking_id')
            ->unique();
        // Update all SlotBooking records with the same Room Type, Date, and Start Time
        SlotBooking::where('room_type', $request->room_type)
            ->where('start_time', $request->start_time)
            ->where('date_for_reservation', $request->date)
            ->update([
                'room_name' => $room->room_name,
                'status' => 1,
                'meeting_link' => $meetingLink,
            ]);

        // Update Slot table status
        Slot::where('start_time', $request->start_time)
            ->where('date_for_reservation', $request->date)
            ->where('room_id', $roomId)
            ->update([
                'slot_status' => 2, // 2 = reserved
            ]);
        //Update booking table room name
        Booking::whereIn('booking_id', $bookingIds)
            ->update([
                'room_name' => $room->room_name,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Room assigned successfully.',
        ]);
    }

    // public function viewingRequestLots(Request $request)
    // {
    //     $lots = SlotBooking::where('bidder_id', $request->bidder_id)
    //         ->where('room_type', $request->room_type)
    //         ->where('start_time', $request->start_time)
    //         ->where('date_for_reservation', $request->date)
    //         ->get();

    //     $allRooms = Room::select('id', 'name', 'type')->get();

    //     return view('admin.lots.viewRequestLots', compact('lots', 'allRooms'));
    // }


    // public function updateLotsStatus(Request $request)
    // {
    //     foreach ($request->input('lot_status', []) as $lotId => $status) {
    //         if (in_array($status, [1, 2])) {
    //             SlotBooking::where('id', $lotId)->update(['status' => $status]);
    //         }
    //     }

    //     return response()->json(['success' => true]);
    // }

    public function updateLotsStatus(Request $request)
    {
        $lotStatusUpdates = $request->input('lot_status', []);
        $updatedSlotIds = [];

        foreach ($lotStatusUpdates as $lotId => $status) {
            if (in_array($status, [1, 2])) {
                $lot = SlotBooking::find($lotId);
                if ($lot) {
                    $lot->status = $status;
                    $lot->save();

                    // Track updated slot_id
                    $updatedSlotIds[] = $lot->slot_id;
                }
            }
        }

        // Update slot status if ANY lot is approved
        foreach (array_unique($updatedSlotIds) as $slotId) {
            $slotLots = SlotBooking::where('slot_id', $slotId)->get();

            // If at least one lot is approved, mark slot as reserved
            if ($slotLots->contains(fn($lot) => $lot->status == 1)) {
                Slot::where('id', $slotId)->update(['slot_status' => 2]); // 2 = Reserved
            }
        }

        return response()->json(['success' => true]);
    }



    // public function updateRequestLotStatus(Request $request, $bookingId)
    // {
    //     try {
    //         $booking = SlotBooking::findOrFail($bookingId);
    //         $booking->status = $request->status;
    //         $booking->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Status updated successfully.',
    //             'slot_id' => $booking->slot_id,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to update status.',
    //         ], 500);
    //     }
    // }



    // public function changeLotStatus($id)
    // {
    //     try {
    //         $lot = Lot::findOrFail($id);
    //         if ($lot->status == 0) {
    //             $lot->status = 1;
    //         } else if ($lot->status == 1) {
    //             $lot->status = 0;
    //         }
    //         $lot->save();
    //         return redirect()->route('admin.lots.index')->with('success', 'Status changed successfully.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Failed to change status: ' . $e->getMessage());
    //     }
    // }
}

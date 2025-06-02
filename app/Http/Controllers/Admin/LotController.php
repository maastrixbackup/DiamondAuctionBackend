<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Lot;
use App\Models\Seller;
use App\Models\Slot;
use App\Models\SlotBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lot::with('seller');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->has('weight') && $request->weight != '') {
            $query->where('weight', 'like', '%' . $request->weight . '%');
        }

        $lots = $query->latest()->get();

        return view('admin.lots.list', compact('lots'));
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
            'type' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'weight' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'stone' => 'nullable|string|max:255',
            'shape' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'batch_code' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
            'report_number' => 'nullable|string|max:255',
            'colour_grade' => 'nullable|string|max:255',
            'colour_origin' => 'nullable|string|max:255',
            'colour_distribution' => 'nullable|string|max:255',
            'polish' => 'nullable|string|max:255',
            'symmetry' => 'nullable|string|max:255',
            'fluorescence' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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
            $lot->type = $request->type;
            $lot->color = $request->color;
            $lot->weight = $request->weight;
            $lot->size = $request->size;
            $lot->stone = $request->stone;
            $lot->shape = $request->shape;
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
            'type' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'weight' => 'required|string|max:255',
            'size' => 'nullable|string|max:255',
            'stone' => 'nullable|string|max:255',
            'shape' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'batch_code' => 'nullable|string|max:255',
            'status' => 'required|in:0,1,2',
            'report_number' => 'nullable|string|max:255',
            'colour_grade' => 'nullable|string|max:255',
            'colour_origin' => 'nullable|string|max:255',
            'colour_distribution' => 'nullable|string|max:255',
            'polish' => 'nullable|string|max:255',
            'symmetry' => 'nullable|string|max:255',
            'fluorescence' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $lot = Lot::findOrFail($id);
        $lot->seller_id = $request->seller_id;
        $lot->category_id = $request->category_id;
        $lot->type = $request->type;
        $lot->color = $request->color;
        $lot->weight = $request->weight;
        $lot->size = $request->size;
        $lot->stone = $request->stone;
        $lot->shape = $request->shape;
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
                    $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
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
        $lot->save();

        return redirect()->route('admin.lots.index')->with('success', 'Lot updated successfully.');
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

        $lot->delete();

        return redirect()->route('admin.lots.index')->with('success', 'Lot deleted successfully.');
    }

    public function viewingRequest(Request $request)
    {
        // $groupedSlots = SlotBooking::select('slot_id', 'bidder_name', 'room_name', 'room_type', 'start_time', 'date_for_reservation')
        //     ->groupBy('slot_id', 'bidder_name', 'room_name', 'room_type', 'start_time', 'date_for_reservation')
        //     ->get();

        $groupedSlots = SlotBooking::whereIn('id', function ($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('slot_bookings')
                ->groupBy('slot_id');
        })
            ->orderByDesc('id')
            ->get();

        return view('admin.lots.viewSlotRequest', compact('groupedSlots'));
    }

    public function viewingRequestLots($slotId)
    {
        $lots = SlotBooking::where('slot_id', $slotId)
            // ->where('status', 0)
            ->get();

        return view('admin.lots.viewRequestLots', compact('lots'));
    }

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

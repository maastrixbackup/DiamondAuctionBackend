<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use Illuminate\Http\Request;

class BidderController extends Controller
{
    public function bidderList(Request $request)
    {
        $bidders = Bidder::all();
        return view('admin.bidder.bidder_list', compact('bidders'));
    }

    public function bidderDetails($id)
    {
        $bidder = Bidder::findOrFail($id);
        return view('admin.bidder.bidder_details', compact('bidder'));
    }

    public function changeBidderKycStatus($id)
    {
        try {
            $bidder = Bidder::findOrFail($id);
            // Cycle status: 0 → 1 → 2 → 0
            $bidder->kyc_status = ($bidder->kyc_status + 1) % 3;
            $bidder->save();

            return redirect()->route('admin.bidder')->with('success', 'KYC status changed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to change KYC status: ' . $e->getMessage());
        }
    }

    public function changeBidderAccountStatus($id)
    {
        try {
            $bidder = Bidder::findOrFail($id);
            // Cycle status: 0 → 1 → 2 → 0
            $bidder->account_status = ($bidder->account_status + 1) % 3;
            $bidder->save();

            return redirect()->route('admin.bidder')->with('success', 'Account status changed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to change Account status: ' . $e->getMessage());
        }
    }
}

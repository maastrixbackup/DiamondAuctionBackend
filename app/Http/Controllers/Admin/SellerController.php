<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function sellerList(Request $request)
    {
        $sellers = Seller::all();
        return view('admin.seller.seller_list', compact('sellers'));
    }

    public function sellerDetails($id)
    {
        $seller = Seller::findOrFail($id);
        return view('admin.seller.seller_details', compact('seller'));
    }

    // public function changeSellerStatus($id)
    // {
    //     try {
    //         $seller = Seller::findOrFail($id);
    //         if ($seller->status == 0) {
    //             $seller->status = 1;
    //         } else if ($seller->status == 1) {
    //             $seller->status = 0;
    //         }
    //         $seller->save();
    //         return redirect()->route('admin.seller')->with('success', 'Status changed successfully.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Failed to change status: ' . $e->getMessage());
    //     }
    // }

    public function changeSellerKycStatus($id)
    {
        try {
            $seller = Seller::findOrFail($id);
            // Cycle status: 0 → 1 → 2 → 0
            $seller->kyc_status = ($seller->kyc_status + 1) % 3;
            $seller->save();

            return redirect()->route('admin.seller')->with('success', 'KYC status changed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to change KYC status: ' . $e->getMessage());
        }
    }

    public function changeSellerAccountStatus($id)
    {
        try {
            $seller = Seller::findOrFail($id);
            // Cycle status: 0 → 1 → 2 → 0
            $seller->account_status = ($seller->account_status + 1) % 3;
            $seller->save();

            return redirect()->route('admin.seller')->with('success', 'Account status changed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to change Account status: ' . $e->getMessage());
        }
    }

}

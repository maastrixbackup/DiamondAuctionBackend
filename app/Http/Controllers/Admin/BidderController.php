<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BidderController extends Controller
{
    public function bidderList(Request $request)
    {
        $bidders = Bidder::orderBy('id', 'desc')->get();
        return view('admin.bidder.bidder_list', compact('bidders'));
    }

    public function bidderDetails($id)
    {
        $bidder = Bidder::findOrFail($id);
        return view('admin.bidder.bidder_details', compact('bidder'));
    }

    public function updateBidderDocumentStatus(Request $request)
    {
        $request->validate([
            'bidder_id' => 'required|exists:bidders,id',
            'field' => 'required|string',
            'status' => 'required|in:1,2', // 1 = Approved, 2 = Rejected
        ]);

        $bidder = Bidder::findOrFail($request->bidder_id);

        // Update specific document's status
        $fieldStatus = $request->field . '_status';
        $bidder->$fieldStatus = $request->status;
        $bidder->save();

        // Determine required document status fields based on bidder type
        $docStatusFields = [];

        if ($bidder->type == 1) {
            $docStatusFields = [
                'certificate_of_incorporation_status',
                'valid_trade_license_status',
                'passport_copy_authorised_status',
                'ubo_declaration_status',
            ];
        } elseif ($bidder->type == 2) {
            $docStatusFields = [
                'passport_copy_status',
                'proof_of_address_status',
            ];
        }

        // Check all document statuses
        $statuses = $bidder->only($docStatusFields);

        if (collect($statuses)->contains(2)) {
            $bidder->kyc_status = 2; // Rejected
        } elseif (collect($statuses)->every(fn($status) => $status == 1)) {
            $bidder->kyc_status = 1; // Approved
        } else {
            $bidder->kyc_status = 0; // Pending
        }

        $bidder->save();

        return back()->with('success', 'Document status updated.');
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

    public function changeBidderAccountStatus($id, $status)
    {
        try {
            $bidder = Bidder::findOrFail($id);

            if (!in_array($status, [1, 2])) {
                return redirect()->back()->with('error', 'Invalid status.');
            }

            $bidder->account_status = $status;
            $bidder->save();

            // Send email only when account is set to Active (1)
            if ($status == 1) {
                $subject = "Account Activated";
                $messageText = "Dear {$bidder->full_name},\n\n" .
                    "Congratulations and thank you for registering with us!\n\n" .
                    "We're pleased to inform you that your account has been **approved** and activated.\n\n" .
                    "You can now log in to your dashboard and secure your spot or book a time slot to start using the platform.\n\n" .
                    "If you have any questions, feel free to reach out to our support team or contact us on WhatsApp.\n\n" .
                    "We’re excited to have you on board!\n\n" .
                    "--\nTeam Support";

                Mail::raw($messageText, function ($message) use ($bidder, $subject) {
                    $message->to($bidder->email_address)
                        ->subject($subject);
                });
            }

            return redirect()->route('admin.bidder')->with('success', 'Account status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update account status: ' . $e->getMessage());
        }
    }
}

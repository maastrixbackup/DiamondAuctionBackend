<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SellerController extends Controller
{
    public function sellerList(Request $request)
    {
        $sellers = Seller::orderBy('id', 'desc')->get();
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

    // public function updateDocumentStatus(Request $request)
    // {
    //     $request->validate([
    //         'seller_id' => 'required|exists:sellers,id',
    //         'field' => 'required|string',
    //         'status' => 'required|in:0,1,2',
    //     ]);

    //     $seller = Seller::findOrFail($request->seller_id);
    //     $field = $request->field . '_status';

    //     if (!in_array($field, [
    //         'certificate_of_incorporation_status',
    //         'valid_trade_license_status',
    //         'passport_copy_authorised_status',
    //         'ubo_declaration_status',
    //         'passport_copy_status',
    //         'proof_of_ownership_status'
    //     ])) {
    //         return back()->with('error', 'Invalid field');
    //     }

    //     $seller->$field = (int)$request->status;
    //     $seller->save();

    //     return back()->with('success', 'Document status updated successfully.');
    // }

    public function updateSellerDocumentStatus(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'field' => 'required|string',
            'status' => 'required|in:1,2', // 1 = Approved, 2 = Rejected
        ]);

        $seller = Seller::findOrFail($request->seller_id);

        // Update specific document's status
        $fieldStatus = $request->field . '_status';
        $seller->$fieldStatus = $request->status;
        $seller->save();

        // Determine required document status fields based on seller type
        $docStatusFields = [];

        if ($seller->type == 1) {
            $docStatusFields = [
                'certificate_of_incorporation_status',
                'valid_trade_license_status',
                'passport_copy_authorised_status',
                'ubo_declaration_status',
                'kyc_document_status',
            ];
        } elseif ($seller->type == 2) {
            $docStatusFields = [
                'passport_copy_status',
                'proof_of_ownership_status',
                'kyc_document_status',
            ];
        }

        // Check all document statuses
        $statuses = $seller->only($docStatusFields);

        if (collect($statuses)->contains(2)) {
            $seller->kyc_status = 2; // Rejected
        } elseif (collect($statuses)->every(fn($status) => $status == 1)) {
            $seller->kyc_status = 1; // Approved
        } else {
            $seller->kyc_status = 0; // Pending
        }

        $seller->save();

        return back()->with('success', 'Document status updated.');
    }


    // public function changeSellerKycStatus($id)
    // {
    //     try {
    //         $seller = Seller::findOrFail($id);
    //         // Cycle status: 0 → 1 → 2 → 0
    //         $seller->kyc_status = ($seller->kyc_status + 1) % 3;
    //         $seller->save();

    //         return redirect()->route('admin.seller')->with('success', 'KYC status changed successfully.');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Failed to change KYC status: ' . $e->getMessage());
    //     }
    // }

    public function changeSellerAccountStatus($id, $status)
    {
        try {
            $seller = Seller::findOrFail($id);

            if (!in_array($status, [1, 2])) {
                return redirect()->back()->with('error', 'Invalid status.');
            }

            $seller->account_status = $status;
            $seller->save();

            // Send email only when account is set to Active (1)
            if ($status == 1) {
                $subject = "Account Activated";
                $messageText = "Dear {$seller->full_name},\n\n" .
                    "Congratulations and thank you for registering with us!\n\n" .
                    "We're pleased to inform you that your account has been **approved** and activated.\n\n" .
                    "You can now log in to your dashboard and secure your spot or book a time slot to start using the platform.\n\n" .
                    "If you have any questions, feel free to reach out to our support team or contact us on WhatsApp.\n\n" .
                    "We’re excited to have you on board!\n\n" .
                    "--\nTeam Support";

                Mail::raw($messageText, function ($message) use ($seller, $subject) {
                    $message->to($seller->email_address)
                        ->subject($subject);
                });
            }

            return redirect()->route('admin.seller')->with('success', 'Account status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update account status: ' . $e->getMessage());
        }
    }
}

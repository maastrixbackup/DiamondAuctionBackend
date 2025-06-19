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
                 $messageText = '
            <html>
              <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
                <table align="center" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; padding: 20px; border: 1px solid #ddd;">
                  <tr>
                    <td style="text-align: center; padding-bottom: 20px;">
                      <img src="https://dexterousdmcc.gemxchange.com/assets/logo-Dhtvyvby.png" alt="Dexterous Tender" style="height: 100px;" />
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <h2 style="color: #333;">Congratulation!</h2>
                      <p style="font-size: 16px; color: #555;">
                        Hi ' . htmlspecialchars($bidder->full_name) . ',
                      </p>
                      <p style="font-size: 16px; color: #555;">
                        Congratulations and thank you for registering with us!
                      </p>
                      <p style="font-size: 16px; color: #555;">
                        We are pleased to inform you that your account has been **approved** and activated.
                      </p>
            
                      <div style="background-color: #f9f9f9; padding: 15px; border: 1px solid #ccc; margin: 20px 0;">
                        <h3 style="color: #444;">Your Login Details</h3>
                        <p style="font-size: 15px;">
                          Username: <strong>' . htmlspecialchars($bidder->email_address) . '</strong>
                        </p>
                        
                      </div>
            
                      <p style="font-size: 16px; color: #555;">
                        You can now log in to your dashboard and secure your spot or book a time slot to start using the platform.
                      </p>
                      
                     <p style="font-size: 16px; color: #555;">
                      We’re excited to have you on board!
                      </p>
            
                      <p style="font-size: 16px; color: #555;">
                        If you have any questions or require help, please call us on 
                        <a href="tel:+4400000000" style="color: #007bff;">+44 xxx xxx xxxx</a> or email us at 
                        <a href="mailto:support@dexteroustender.com" style="color: #007bff;">support@dexteroustender.com</a>.
                      </p>
            
                      <hr style="margin: 30px 0;" />
            
                      <h3 style="color: #333;">Buyer\'s Premium</h3>
                      <p style="font-size: 15px; color: #555;">
                        On the first £100,000 of the Hammer Price (of any individual lot), the buyer will pay the hammer price and a premium of 
                        <strong>25% (plus VAT)</strong> or <strong>30% (inclusive of VAT)</strong>.<br />
                        On the excess over £100,001 of the hammer price (of any individual lot), the buyer will pay the hammer price and a premium of 
                        <strong>15% (plus VAT)</strong> or <strong>18% (inclusive of VAT)</strong>.
                      </p>
            
                      <h3 style="color: #333;">Seller\'s Commission</h3>
                      <p style="font-size: 15px; color: #555;">
                        Our seller’s commission charge is <strong>15% (plus VAT)</strong>. A marketing fee is charged at <strong>£10 (plus VAT)</strong> per lot.<br />
                        There is also a loss/liability charge of <strong>1.5% (plus VAT)</strong> per lot.<br />
                        We offer free worldwide shipping subject to our T&Cs.
                      </p>
            
                      <p style="font-size: 14px; color: #999; margin-top: 30px;">--<br />Team Dexterous</p>
                    </td>
                  </tr>
                </table>
            
                <table align="center" width="600" style="font-size: 12px; color: #999; text-align: center; margin-top: 20px;">
                  <tr>
                    <td>
                      <p style="margin-bottom: 5px;">
                        © ' . date('Y') . ' Dexterous Tender. All rights reserved.
                      </p>
                      <p>
                        You are receiving this email at <a href="mailto:' . htmlspecialchars($bidder->email_address) . '" style="color: #999;">' . htmlspecialchars($bidder->email_address) . '</a>
                      </p>
                    </td>
                  </tr>
                </table>
              </body>
            </html>';

                Mail::html($messageText, function ($message) use ($bidder, $subject) {
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

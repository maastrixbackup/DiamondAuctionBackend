<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function adminList(Request $request)
    {
        $admins = Admin::where('role', 'superadmin')
            ->orderBy('id', 'desc')
            ->where('id', '!=', 1)
            ->get();
        return view('admin.admin.admin_list', compact('admins'));
    }

    public function adminDetails($id)
    {
        $admin = Admin::where('id', $id)->where('role', 'superadmin')->firstOrFail();

        return view('admin.admin.admin_details', compact('admin'));
    }

    public function showChangePasswordForm()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.admin.change_password', compact('admin'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        if (Hash::check($request->new_password, $admin->password)) {
            return back()->with('error', 'New password must be different from the current password.');
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Password changed successfully. Please log in again.');
    }
}

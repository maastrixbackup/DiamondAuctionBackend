<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function adminList(Request $request)
    {
        $admins = Admin::where('role', 'admin')->get();
        return view('admin.admin.admin_list', compact('admins'));
    }

    public function adminDetails($id)
    {
        $admin = Admin::where('id', $id)->where('role', 'admin')->firstOrFail();

        return view('admin.admin.admin_details', compact('admin'));
    }
}

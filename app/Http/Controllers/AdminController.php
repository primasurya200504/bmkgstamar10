<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $submissions = Submission::with('user')->orderBy('created_at', 'desc')->get();

        return view('admin.admin_dashboard', compact('submissions'));
    }
}

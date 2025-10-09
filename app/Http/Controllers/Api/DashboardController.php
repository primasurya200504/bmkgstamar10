<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $total_applications = $user->applications()->count();
        $pending = $user->applications()->where('status', 'pending')->count();
        $approved = $user->applications()->where('status', 'approved')->count();
        $total_certificates = $user->replacementCertificates()->count();
        return response()->json([
            'total_applications' => $total_applications,
            'pending_applications' => $pending,
            'approved_applications' => $approved,
            'total_certificates' => $total_certificates,
        ]);
    }
}

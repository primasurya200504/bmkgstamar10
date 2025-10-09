<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReplacementCertificate;
use Illuminate\Http\Request;

class ReplacementCertificateController extends Controller
{
    public function index(Request $request)
    {
        $certificates = $request->user()->replacementCertificates;
        return response()->json($certificates);
    }
}

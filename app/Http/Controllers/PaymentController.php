<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())->latest()->get();
        return view('user.payments.index', compact('payments'));
    }

    public function uploadProof(Request $request, $id)
    {
        $payment = Payment::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'proof_file' => 'required|file|mimes:jpg,png,pdf',
        ]);

        $path = $request->file('proof_file')->store('proofs');

        $payment->update([
            'proof_file' => $path,
            'status' => 'uploaded',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}

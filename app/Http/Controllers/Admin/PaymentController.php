<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('user')->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.payments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ebilling_file' => 'required|file|mimes:pdf,jpg,png',
        ]);

        $path = $request->file('ebilling_file')->store('ebillings');

        Payment::create([
            'user_id' => $request->user_id,
            'ebilling_file' => $path,
            'status' => 'pending',
        ]);

        // ✅ redirect konsisten ke admin.payments.index
        return redirect()->route('admin.payments.index')
            ->with('success', 'E-Billing berhasil dikirim ke user.');
    }

    public function verify($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'verified']);

        return back()->with('success', 'Pembayaran user sudah diverifikasi.');
    }
}

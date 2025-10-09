<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nip' => 'nullable|string|max:255|unique:teachers,nip',
            'department' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'username' => $validated['username'],
                'password_hash' => Hash::make($validated['password']),
                'role' => 'user',
                'status' => 'active',
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'department' => $validated['department'],
                'title' => $validated['title'],
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Guru berhasil ditambahkan!'
                ]);
            }

            return redirect()->route('admin.users')->with('success', 'Guru berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan guru: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menambahkan guru: ' . $e->getMessage());
        }
    }
}

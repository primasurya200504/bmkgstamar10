@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Admin Dashboard - Submission List</h1>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200">
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">ID</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Submission Number</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">User Name</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Date Submitted</th>
                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $submission)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-4">{{ $submission->id }}</td>
                        <td class="py-3 px-4">
                            {{ $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="py-3 px-4">{{ $submission->user->name ?? 'N/A' }}</td>
                        <td class="py-3 px-4">{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">{{ ucfirst($submission->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">No submissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

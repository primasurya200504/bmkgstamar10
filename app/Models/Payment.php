<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id', // CHANGED from application_id
        'amount',
        'status',
        'payment_proof',
        'payment_method',
        'payment_reference',
        'paid_at',
        'verified_at',
        'verified_by',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    /**
     * UPDATED: Relationship dengan Submission (bukan Application)
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Backward compatibility - alias untuk submission
     */
    public function application()
    {
        return $this->submission();
    }

    /**
     * Relationship dengan User (verifier)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Pembayaran',
            'uploaded' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak'
        ];

        return $labels[$this->status] ?? 'Status Tidak Dikenal';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'uploaded' => 'bg-blue-100 text-blue-800',
            'verified' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if payment proof exists
     */
    public function hasProof()
    {
        return !empty($this->payment_proof);
    }
}

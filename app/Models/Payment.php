<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'amount',
        'payment_proof',
        'payment_method',
        'payment_reference',
        'status',
        'paid_at',
        'verified_at',
        'verified_by',
        'e_billing_path',
        'e_billing_filename',
    ];

    public function user()
    {
        return $this->hasOneThrough(User::class, Submission::class, 'id', 'id', 'submission_id', 'user_id');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}

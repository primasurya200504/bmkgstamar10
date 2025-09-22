<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'amount', 'payment_proof', 'status', 'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

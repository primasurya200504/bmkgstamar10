<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'action', 'actor_type', 'actor_id',
        'title', 'description', 'metadata', 'created_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];

    public $timestamps = false; // Karena kita hanya gunakan created_at

    // Relationships
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    // Helper untuk icon berdasarkan action
    public function getIconAttribute()
    {
        $icons = [
            'submitted' => 'paper-airplane',
            'approved_with_payment' => 'check-circle',
            'approved_no_payment' => 'check-circle',
            'rejected' => 'x-circle',
            'payment_uploaded' => 'credit-card',
            'payment_verified' => 'badge-check',
            'payment_rejected' => 'x-circle',
            'document_uploaded' => 'document-arrow-up',
            'completed' => 'check-badge',
            'archived' => 'archive-box'
        ];

        return $icons[$this->action] ?? 'information-circle';
    }

    // Helper untuk warna berdasarkan action
    public function getColorAttribute()
    {
        $colors = [
            'submitted' => 'blue',
            'approved_with_payment' => 'green',
            'approved_no_payment' => 'green',
            'rejected' => 'red',
            'payment_uploaded' => 'yellow',
            'payment_verified' => 'green',
            'payment_rejected' => 'red',
            'document_uploaded' => 'indigo',
            'completed' => 'green',
            'archived' => 'gray'
        ];

        return $colors[$this->action] ?? 'gray';
    }
}

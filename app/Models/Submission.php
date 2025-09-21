<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'submission_number',
        'data_type',
        'start_date',
        'end_date',
        'purpose',
        'category',
        'status',
        'rejection_note',
        'admin_notes',
        'cover_letter_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'Menunggu' => 'yellow',
            'Diproses' => 'blue',
            'Diterima' => 'green',
            'Ditolak' => 'red',
            'Selesai' => 'green',
            default => 'gray'
        };
    }
}

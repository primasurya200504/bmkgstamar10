<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guideline_id',
        'submission_number',
        'type',
        'start_date',
        'end_date',
        'purpose',
        'status',
        'rejection_note',
        'admin_notes',
        'cover_letter_path',
        'documents'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'documents' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guideline()
    {
        return $this->belongsTo(Guideline::class);
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function histories()
    {
        return $this->hasMany(SubmissionHistory::class);
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }

    /**
     * Log history for this submission
     */
    public function logHistory($action, $actorType, $actorId, $title, $description)
    {
        return $this->histories()->create([
            'actor_id' => $actorId,
            'actor_type' => $actorType,
            'action' => $action,
            'title' => $title,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guideline_id',
        'application_number',
        'type',
        'documents',
        'start_date',
        'end_date',
        'purpose',
        'status',
        'notes'
    ];

    protected $casts = [
        'documents' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guideline()
    {
        return $this->belongsTo(Guideline::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    // TAMBAHAN: Relasi ke histories
    public function histories()
    {
        return $this->hasMany(ApplicationHistory::class)->orderBy('created_at', 'desc');
    }

    // Helper method untuk log history
    public function logHistory($action, $actorType, $actorId, $title, $description = null, $metadata = null)
    {
        return ApplicationHistory::log(
            $this->id,
            $action,
            $actorType,
            $actorId,
            $title,
            $description,
            $metadata
        );
    }
}

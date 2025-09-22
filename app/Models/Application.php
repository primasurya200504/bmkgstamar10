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
        'status',
        'notes'
    ];

    protected $casts = [
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

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function archive()
    {
        return $this->hasOne(Archive::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            $application->application_number = 'APP-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}

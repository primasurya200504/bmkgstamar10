<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'archive_date',
        'notes'
    ];

    protected $casts = [
        'archive_date' => 'datetime'
    ];

    // TAMBAHKAN: Auto-fill archive_date jika tidak ada
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($archive) {
            if (!$archive->archive_date) {
                $archive->archive_date = now();
            }
        });
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'application_id');
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class, 'submission_id', 'application_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

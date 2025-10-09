<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'user_id',
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
        return $this->belongsTo(Submission::class);
    }

    public function files()
    {
        return $this->hasMany(SubmissionFile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

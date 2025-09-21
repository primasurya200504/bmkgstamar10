<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function getFileSizeHumanAttribute()
    {
        $size = $this->file_size;
        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return round($size / 1024, 2) . ' KB';
        } else {
            return $size . ' bytes';
        }
    }
}

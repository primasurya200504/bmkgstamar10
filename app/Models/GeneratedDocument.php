<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id', // CHANGED from application_id
        'document_path',
        'document_name',
        'document_type',
        'file_size',
        'mime_type',
        'uploaded_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * UPDATED: Relationship dengan Submission (bukan Application)
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Backward compatibility - alias untuk submission
     */
    public function application()
    {
        return $this->submission();
    }

    /**
     * Relationship dengan User (uploader)
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file extension
     */
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->document_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is image
     */
    public function isImage()
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        return in_array(strtolower($this->file_extension), $imageTypes);
    }

    /**
     * Check if file is PDF
     */
    public function isPdf()
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    /**
     * Get uploader name
     */
    public function getUploaderNameAttribute()
    {
        return $this->uploader ? $this->uploader->name : 'System';
    }
}

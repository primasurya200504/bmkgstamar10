<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'document_name',
        'document_path',
        'document_type',
        'file_size'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

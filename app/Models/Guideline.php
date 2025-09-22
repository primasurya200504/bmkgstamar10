<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guideline extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'required_documents',
        'fee',
        'is_active'
    ];

    protected $casts = [
        'required_documents' => 'array'
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}

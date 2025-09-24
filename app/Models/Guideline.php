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
        'type',  // ADDED untuk support seeder
        'fee',
        'required_documents',
        'is_active'
    ];

    protected $casts = [
        'required_documents' => 'array',
        'is_active' => 'boolean',
        'fee' => 'integer'
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function getFormattedFeeAttribute()
    {
        return 'Rp ' . number_format($this->fee, 0, ',', '.');
    }
}

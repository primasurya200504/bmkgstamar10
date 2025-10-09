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
        'fee',
        'required_documents',
        'is_active'
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'is_active' => 'boolean'
        // Removed 'required_documents' => 'array' to use accessor for safe handling
    ];

    // Accessor untuk required_documents yang aman
    public function getRequiredDocumentsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    // Mutator untuk required_documents
    public function setRequiredDocumentsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['required_documents'] = json_encode($value);
        } elseif (is_string($value)) {
            $this->attributes['required_documents'] = $value;
        } else {
            $this->attributes['required_documents'] = json_encode([]);
        }
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}

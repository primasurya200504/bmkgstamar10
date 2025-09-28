<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionHistory extends Model
{
    use HasFactory;

    protected $table = 'submission_histories';

    protected $fillable = [
        'submission_id',
        'action',
        'actor_type',
        'actor_id',
        'title',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship dengan Submission
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Backward compatibility
     */
    public function application()
    {
        return $this->submission();
    }

    /**
     * Relationship dengan User (actor)
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * Get actor name
     */
    public function getActorNameAttribute()
    {
        return $this->actor ? $this->actor->name : 'System';
    }

    /**
     * Get action badge class for UI
     */
    public function getActionBadgeClassAttribute()
    {
        $badges = [
            'submitted' => 'bg-blue-100 text-blue-800',
            'verified' => 'bg-green-100 text-green-800',
            'payment_uploaded' => 'bg-yellow-100 text-yellow-800',
            'payment_verified' => 'bg-purple-100 text-purple-800',
            'processing' => 'bg-indigo-100 text-indigo-800',
            'completed' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'document_uploaded' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->action] ?? 'bg-gray-100 text-gray-800';
    }
}

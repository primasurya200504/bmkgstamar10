<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'action',
        'actor_type',
        'actor_id',
        'title',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime'
    ];

    public $timestamps = false;

    protected $dates = ['created_at'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public static function log($applicationId, $action, $actorType, $actorId, $title, $description = null, $metadata = null)
    {
        return self::create([
            'application_id' => $applicationId,
            'action' => $action,
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'created_at' => now()
        ]);
    }
}

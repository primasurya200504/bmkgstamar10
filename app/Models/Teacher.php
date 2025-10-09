<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'unsignedBigInteger';

    protected $fillable = [
        'user_id',
        'nip',
        'department',
        'title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false; // only created_at

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'target',
        'payload',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'actor_id' => 'integer',
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    public $timestamps = false; // only created_at

    protected $fillable = [
        'admin_id',
        'filename',
        'total_records',
        'inserted',
        'updated',
        'failed',
        'details',
        'created_at',
    ];

    protected $casts = [
        'admin_id' => 'integer',
        'total_records' => 'integer',
        'inserted' => 'integer',
        'updated' => 'integer',
        'failed' => 'integer',
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    use HasFactory;

    public $timestamps = false; // table uses custom timestamps

    protected $fillable = [
        'type',
        'identifier',
        'name',
        'class',
        'major',
        'position',
        'token_hash',
        'has_voted',
        'last_voted_at',
        'imported_at',
        'year',
    ];

    protected $casts = [
        'has_voted' => 'boolean',
        'last_voted_at' => 'datetime',
        'imported_at' => 'datetime',
        'year' => 'integer',
    ];

    // Relationships
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

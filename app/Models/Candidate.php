<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    public $timestamps = false; // using manual timestamps

    protected $fillable = [
        'ballot_number',
        'leader_name',
        'deputy_name',
        'photo_path',
        'vision',
        'mission',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'ballot_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function candidateElections()
    {
        return $this->hasMany(CandidateElection::class);
    }

    public function elections()
    {
        return $this->belongsToMany(Election::class, 'candidate_election')
            ->withPivot('ballot_number');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

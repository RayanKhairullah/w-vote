<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    public $timestamps = false; // using DB-managed timestamps

    protected $fillable = [
        'name',
        'year',
        'start_at',
        'end_at',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function candidates()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_election')
            ->withPivot('ballot_number');
    }

    public function candidateElections()
    {
        return $this->hasMany(CandidateElection::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

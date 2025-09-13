<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateElection extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'candidate_election';

    protected $fillable = [
        'election_id',
        'candidate_id',
        'ballot_number',
    ];

    protected $casts = [
        'election_id' => 'integer',
        'candidate_id' => 'integer',
        'ballot_number' => 'integer',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

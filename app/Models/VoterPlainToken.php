<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoterPlainToken extends Model
{
    use HasFactory;

    protected $table = 'voter_plain_tokens';

    protected $fillable = [
        'voter_id',
        'token_encrypted',
    ];

    public $timestamps = true;
}

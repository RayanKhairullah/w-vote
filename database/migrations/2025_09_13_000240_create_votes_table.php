<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->unsignedInteger('election_id');
            $table->unsignedBigInteger('voter_id');
            $table->unsignedInteger('candidate_id');
            $table->string('token_hash_used', 255);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade');
            $table->foreign('voter_id')->references('id')->on('voters')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');

            $table->index('candidate_id', 'idx_votes_candidate');
            $table->index('voter_id', 'idx_votes_voter');
            $table->unique(['election_id', 'voter_id'], 'ux_vote_once_per_election');
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->index(['election_id', 'candidate_id'], 'ix_votes_election_candidate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_election', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->unsignedInteger('election_id');
            $table->unsignedInteger('candidate_id');
            $table->unsignedSmallInteger('ballot_number')->nullable();

            $table->foreign('election_id')->references('id')->on('elections')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('candidates')->onDelete('cascade');
            $table->unique(['election_id', 'candidate_id'], 'ux_candidate_election');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_election');
    }
};

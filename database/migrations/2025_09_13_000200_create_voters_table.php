<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voters', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->enum('type', ['student', 'staff']);
            $table->string('identifier', 50); // nisn or nip
            $table->string('name', 200);
            $table->string('class', 100)->nullable();
            $table->string('major', 100)->nullable();
            $table->string('position', 150)->nullable();
            $table->string('token_hash', 255);
            $table->boolean('has_voted')->default(false);
            $table->timestamp('last_voted_at')->nullable();
            $table->timestamp('imported_at')->useCurrent();
            $table->unsignedSmallInteger('year');

            $table->unique(['identifier', 'year'], 'ux_voter_identifier_year');
            $table->index('type', 'idx_voter_type');
            $table->index('has_voted', 'idx_voter_has_voted');
        });

        // Additional composite index
        Schema::table('voters', function (Blueprint $table) {
            $table->index(['year', 'has_voted'], 'ix_voters_year_has_voted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};

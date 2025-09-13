<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('ballot_number');
            $table->string('leader_name', 200);
            $table->string('deputy_name', 200);
            $table->string('photo_path', 500)->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();

            // As per SQL: unique on ballot_number (note: global unique; per-election uniqueness is handled in pivot if needed)
            $table->unique('ballot_number', 'ux_ballot_number_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};

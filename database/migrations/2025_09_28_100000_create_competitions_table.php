<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // association to season
            $table->string('season_id');

            // competition attributes
            $table->string('name');
            $table->enum('type', ['team_league', 'team_cup', 'individ_cup', 'pairs_cup']);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['season_id', 'type']);
            $table->unique(['season_id', 'name']);

            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            if (Schema::hasColumn('competitions', 'season_id')) {
                $table->dropForeign(['season_id']);
            }
        });

        Schema::dropIfExists('competitions');
    }
};

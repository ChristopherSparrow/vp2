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
        Schema::create('games', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->string('competition_id');

            // team-based references (nullable depending on competition type)
            $table->string('home_team_id')->nullable();
            $table->string('away_team_id')->nullable();

            // individual-based references (nullable depending on competition type)
            $table->string('home_indiv_id')->nullable();
            $table->string('away_indiv_id')->nullable();

            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();

            // scheduled date/time for the game
            $table->dateTime('date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['competition_id', 'date']);

            $table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');

            $table->foreign('home_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('away_team_id')->references('id')->on('teams')->onDelete('cascade');

            $table->foreign('home_indiv_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('away_indiv_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            if (Schema::hasColumn('games', 'competition_id')) {
                $table->dropForeign(['competition_id']);
            }
            if (Schema::hasColumn('games', 'home_team_id')) {
                $table->dropForeign(['home_team_id']);
            }
            if (Schema::hasColumn('games', 'away_team_id')) {
                $table->dropForeign(['away_team_id']);
            }
            if (Schema::hasColumn('games', 'home_indiv_id')) {
                $table->dropForeign(['home_indiv_id']);
            }
            if (Schema::hasColumn('games', 'away_indiv_id')) {
                $table->dropForeign(['away_indiv_id']);
            }
        });

        Schema::dropIfExists('games');
    }
};

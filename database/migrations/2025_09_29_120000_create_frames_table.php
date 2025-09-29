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
        Schema::create('frames', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // reference to games (team_league games)
            $table->string('game_id');

            // player references (nullable depending on game type)
            $table->string('home_player')->nullable();
            $table->string('away_player')->nullable();

            // frame number within the game (1-12)
            $table->unsignedTinyInteger('game_no')->nullable();

            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();

            // NOTE: column names cannot start with a digit in most databases.
            // Using 'eight_ball_clear_home' and 'eight_ball_clear_away' instead of '8_ball_clear_*'.
            $table->boolean('eight_ball_clear_home')->default(false);
            $table->boolean('eight_ball_clear_away')->default(false);

            $table->unsignedTinyInteger('home_game_no')->nullable();
            $table->unsignedTinyInteger('away_game_no')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['game_id', 'game_no']);

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');

            $table->foreign('home_player')->references('id')->on('players')->onDelete('set null');
            $table->foreign('away_player')->references('id')->on('players')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frames', function (Blueprint $table) {
            if (Schema::hasColumn('frames', 'game_id')) {
                $table->dropForeign(['game_id']);
            }
            if (Schema::hasColumn('frames', 'home_player')) {
                $table->dropForeign(['home_player']);
            }
            if (Schema::hasColumn('frames', 'away_player')) {
                $table->dropForeign(['away_player']);
            }
        });

        Schema::dropIfExists('frames');
    }
};

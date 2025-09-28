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
        Schema::create('player_teams', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // references to player and team
            $table->string('player_id');
            $table->string('team_id');

            // membership window
            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['player_id', 'team_id']);

            $table->foreign('player_id')->references('id')->on('players')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_teams', function (Blueprint $table) {
            if (Schema::hasColumn('player_teams', 'player_id')) {
                $table->dropForeign(['player_id']);
            }
            if (Schema::hasColumn('player_teams', 'team_id')) {
                $table->dropForeign(['team_id']);
            }
        });

        Schema::dropIfExists('player_teams');
    }
};

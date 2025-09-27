<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // basic identity
            $table->string('name');
            $table->string('abbreviation', 10)->nullable();
            $table->string('slug')->unique();

            // location fields
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('USA');

            // association to season (each team belongs to one season)
            $table->ulid('season_id');

            $table->timestamps();
            $table->softDeletes();

            // indexes and foreign keys
            $table->index(['name', 'city']);
            $table->index('season_id');

            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
};

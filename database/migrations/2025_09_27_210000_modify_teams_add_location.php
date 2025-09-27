<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new location column
        Schema::table('teams', function (Blueprint $table) {
            $table->string('location')->nullable()->after('name');
        });

        // Populate location from city/state/country where present
        DB::table('teams')->get()->each(function ($team) {
            $parts = array_filter([$team->city ?? null, $team->state ?? null, $team->country ?? null]);
            $location = $parts ? implode(', ', $parts) : null;
            DB::table('teams')->where('id', $team->id)->update(['location' => $location]);
        });

        // Try dropping old columns; some platforms (sqlite without dbal) may fail
        try {
            Schema::table('teams', function (Blueprint $table) {
                if (Schema::hasColumn('teams', 'slug')) {
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('teams', 'abbreviation')) {
                    $table->dropColumn('abbreviation');
                }
                if (Schema::hasColumn('teams', 'city')) {
                    $table->dropColumn(['city','state','country']);
                }
            });
        } catch (\Exception $e) {
            // On DBs like sqlite without doctrine/dbal, dropColumn may fail.
            // We'll leave the old columns in place if dropping fails.
            // Log the issue to the application log so the developer can address it.
            logger()->warning('Could not drop old team location/slug columns automatically. Please run a manual migration or install doctrine/dbal to enable column drops.', ['exception' => $e->getMessage()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate dropped columns if missing
        Schema::table('teams', function (Blueprint $table) {
            if (!Schema::hasColumn('teams', 'abbreviation')) {
                $table->string('abbreviation', 10)->nullable()->after('name');
            }
            if (!Schema::hasColumn('teams', 'slug')) {
                $table->string('slug')->unique()->after('abbreviation');
            }
            if (!Schema::hasColumn('teams', 'city')) {
                $table->string('city')->nullable()->after('abbreviation');
                $table->string('state')->nullable()->after('city');
                $table->string('country')->default('USA')->after('state');
            }

            if (Schema::hasColumn('teams', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};

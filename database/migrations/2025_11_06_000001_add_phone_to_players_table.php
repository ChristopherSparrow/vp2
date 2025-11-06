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
        if (! Schema::hasTable('players')) return;

        // add phone column
        if (! Schema::hasColumn('players', 'phone')) {
            Schema::table('players', function (Blueprint $table) {
                $table->string('phone')->nullable();
            });
        }

        // copy existing number values into phone (if present)
        $rows = DB::table('players')->get();
        foreach ($rows as $r) {
            if (property_exists($r, 'number') && $r->number !== null) {
                DB::table('players')->where('id', $r->id)->update(['phone' => (string) $r->number]);
            }
        }

        // drop the old number column if it exists
        if (Schema::hasColumn('players', 'number')) {
            Schema::table('players', function (Blueprint $table) {
                $table->dropColumn('number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('players')) return;

        // re-add number column
        if (! Schema::hasColumn('players', 'number')) {
            Schema::table('players', function (Blueprint $table) {
                $table->unsignedInteger('number')->nullable();
            });
        }

        // copy phone values back into number where numeric
        $rows = DB::table('players')->get();
        foreach ($rows as $r) {
            if (property_exists($r, 'phone') && $r->phone !== null && is_numeric($r->phone)) {
                DB::table('players')->where('id', $r->id)->update(['number' => (int) $r->phone]);
            }
        }

        // drop the phone column
        if (Schema::hasColumn('players', 'phone')) {
            Schema::table('players', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
};

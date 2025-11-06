<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'phone',
    ];

    protected $casts = [
        'phone' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * (No relationships defined for Player)
     */
    public function playerTeams()
    {
        return $this->hasMany(PlayerTeam::class);
    }

    /**
     * Teams the player has been associated with (through PlayerTeam records).
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'player_teams', 'player_id', 'team_id')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }

    /**
     * Return the player's current team (pivot with null end_date) or the most recent by start_date.
     */
    public function currentTeam()
    {
        // If relations are already loaded, operate on them to avoid extra queries
        if ($this->relationLoaded('playerTeams')) {
            $pts = $this->playerTeams->filter(function ($pt) {
                return empty($pt->deleted_at);
            })->sortByDesc('start_date');

            // prefer active assignment (no end_date)
            $active = $pts->firstWhere('end_date', null);
            if ($active) {
                return $active->team;
            }

            $latest = $pts->first();
            return $latest ? $latest->team : null;
        }

        // fallback: query joined relationship
        $pt = $this->playerTeams()->whereNull('end_date')->latest('start_date')->with('team')->first();
        if ($pt) return $pt->team;

        $pt = $this->playerTeams()->latest('start_date')->with('team')->first();
        return $pt ? $pt->team : null;
    }
}

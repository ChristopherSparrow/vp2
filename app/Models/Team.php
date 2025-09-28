<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'location',
        'season_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * A team belongs to a single season.
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * PlayerTeam records for this team.
     */
    public function playerTeams()
    {
        return $this->hasMany(PlayerTeam::class);
    }

    /**
     * Players that have been associated with this team.
     */
    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_teams', 'team_id', 'player_id')
            ->withPivot(['start_date', 'end_date'])
            ->withTimestamps();
    }
}

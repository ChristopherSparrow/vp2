<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Ensure a ULID is generated for non-incrementing string primary keys.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->getKey())) {
                $model->{$model->getKeyName()} = (string) Str::ulid();
            }
        });
    }

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'home_indiv_id',
        'away_indiv_id',
        'home_score',
        'away_score',
        'date',
    ];

    protected $casts = [
        'home_score' => 'integer',
        'away_score' => 'integer',
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function homePlayer()
    {
        return $this->belongsTo(Player::class, 'home_indiv_id');
    }

    public function awayPlayer()
    {
        return $this->belongsTo(Player::class, 'away_indiv_id');
    }

    public function frames()
    {
        return $this->hasMany(Frame::class, 'game_id');
    }

    /**
     * Return frames ordered by game_no (query builder).
     */
    public function orderedFrames()
    {
        return $this->frames()->orderBy('game_no');
    }

    /**
     * Return a collection of ordered frames with per-frame appearance numbers for
     * the players. This computes, for each frame, how many times the player has
     * appeared (in either home or away) up to and including that frame. The
     * resulting Frame models will have dynamic properties added:
     * - home_appearance_number
     * - away_appearance_number
     *
     * This keeps presentation logic out of the Blade view.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function framesWithAppearanceNumbers()
    {
        $frames = $this->orderedFrames()->get();

        $appearance = []; // player_id => count

        foreach ($frames as $frame) {
            // gather unique players in this frame to avoid double-counting
            $playersThisFrame = [];
            if ($frame->home_player) {
                $playersThisFrame[$frame->home_player] = 'home';
            }
            if ($frame->away_player) {
                // If the same player appears both sides, keep both roles but only count once
                $playersThisFrame[$frame->away_player] = isset($playersThisFrame[$frame->away_player])
                    ? $playersThisFrame[$frame->away_player] . '|away'
                    : 'away';
            }

            // For each unique player appearing in this frame, increment their counter
            foreach ($playersThisFrame as $pid => $roles) {
                if (! isset($appearance[$pid])) {
                    $appearance[$pid] = 0;
                }
                $appearance[$pid]++;
            }

            // Attach appearance numbers to the frame for the specific roles
            if ($frame->home_player) {
                $frame->home_appearance_number = $appearance[$frame->home_player] ?? 0;
            } else {
                $frame->home_appearance_number = 0;
            }

            if ($frame->away_player) {
                $frame->away_appearance_number = $appearance[$frame->away_player] ?? 0;
            } else {
                $frame->away_appearance_number = 0;
            }
        }

        return $frames;
    }
}

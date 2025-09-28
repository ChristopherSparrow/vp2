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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Frame extends Model
{
    use HasFactory, SoftDeletes;

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
        'game_id',
        'home_player',
        'away_player',
        'game_no',
        'home_score',
        'away_score',
        'eight_ball_clear_home',
        'eight_ball_clear_away',
        'home_game_no',
        'away_game_no',
    ];

    protected $casts = [
        'game_no' => 'integer',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'eight_ball_clear_home' => 'boolean',
        'eight_ball_clear_away' => 'boolean',
        'home_game_no' => 'integer',
        'away_game_no' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function homePlayer()
    {
        return $this->belongsTo(Player::class, 'home_player');
    }

    public function awayPlayer()
    {
        return $this->belongsTo(Player::class, 'away_player');
    }
}

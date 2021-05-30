<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TournamentEntry extends Model
{
    use HasFactory;

    protected $with = [
        'player'
    ];

    protected $fillable = [
        'tournament_id',
        'licence_number',
        'current_ranking',
        'user_id'
    ];

    protected $table = 'tournament_entries';

    public static $validation_rules = [
        'tournament_id' => [
            'required',
            'exists:tournaments,id'
        ],
        'licence_number' => [
            'required',
            'string',
            'min:8',
            'max:255',
        ],
        'current_ranking' => [
            'required',
            'integer',
            'gt:0'
        ]
    ];

    public function tournament(): BelongsTo {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'id')->orderBy('date', 'desc');
    }

    public function player(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function duels() {
        return $this->duel_first->merge($this->duel_second)->unique();
    }

    private function duel_first() {
        return $this->hasMany(TournamentDuel::class, 'first_user_entry_id', 'id');
    }

    private function duel_second() {
        return $this->hasMany(TournamentDuel::class, 'second_user_entry_id', 'id');
    }

}

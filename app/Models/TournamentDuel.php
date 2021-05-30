<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentDuel extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'first_user_entry_id',
        'second_user_entry_id',
        'first_player_win',
        'second_player_win',
    ];

    function tournament(): BelongsTo {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'id');
    }

    function first_entry(): BelongsTo {
        return $this->belongsTo(TournamentEntry::class, 'first_user_entry_id', 'id');
    }

    function second_entry(): BelongsTo {
        return $this->belongsTo(TournamentEntry::class, 'second_user_entry_id', 'id');
    }
}

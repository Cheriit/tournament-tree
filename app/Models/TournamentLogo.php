<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentLogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'path',
        'name'
    ];

    protected $table = 'tournament_logos';

    public static $validation_rules = [
        'file' => [
            'required',
            'mimes:png,jpg,svg',
            'max:4096'
        ]
    ];

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class, 'tournament_id', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class Tournament extends Model
{
    use HasFactory;

    protected $with = [
        'category',
        'organisator',
        'entries',
        'duels',
        'logos'
    ];

    protected $fillable = [
        'name',
        'place',
        'latitude',
        'longtitude',
        'date',
        'max_participants',
        'ranked_players',
        'organisator_id',
        'category_id'
    ];

    protected $table = 'tournaments';

    public static $validation_rules = [
        'name' => [
            'required',
            'string',
            'min:8',
            'max:255'
        ],
        'place' => [
            'required',
            'string',
            'min:8',
            'max:255'
        ],
        'latitude' => [
            'required_with:longitude',
            'nullable',
            'numeric',
        ],
        'longitude' => [
            'required_with:latitude',
            'nullable',
            'numeric',
        ],
        'date' => [
            'required',
            'date',
            'after:tomorrow'
        ],
        'max_participants' => [
            'required',
            'gte:2',
        ],
        'ranked_players' => [
            'required',
            'gte:2',
        ],
        'category_id' => [
            'required',
            'exists:categories,id'
        ]
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function organisator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organisator_id', 'id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(TournamentEntry::class, 'tournament_id', 'id')->orderBy('current_position')->orderBy('current_ranking');
    }

    public function duels(): HasMany
    {
        return $this->hasMany(TournamentDuel::class, 'tournament_id', 'id');
    }

    public function logos(): HasMany
    {
        return $this->hasMany(TournamentLogo::class, 'tournament_id', 'id');
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('name');
    }

    public function scopeFilter($query, $name, $category) {
        if ($name !== null) {
            $query->where(function ($query) use ($name){
               $query->where('name', 'like', '%'.$name.'%')
                   ->orWhere('place', 'like', '%'.$name.'%')
                   ->orWhereHas('organisator', function ($query) use ($name) {
                       $query->where('name', 'like', '%'.$name.'%');
                   });
            });
        }
        if ($category !== null ) {
            $query->where('category_id', 'like', $category);
        }
    }


}

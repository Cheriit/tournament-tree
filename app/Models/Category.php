<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [ "name" ];

    public static $validation_rules = [
        'name' => [
            'required',
            'string',
            'unique:categories',
            'min:8',
            'max:255'
        ]
    ];

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'category_id', 'id');
    }
}

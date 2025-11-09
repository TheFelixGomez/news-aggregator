<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'api_source_id'];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function preferredByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_preferred_source');
    }
}

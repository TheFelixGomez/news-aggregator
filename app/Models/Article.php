<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'title',
        'slug',
        'description',
        'content',
        'url',
        'image_url',
        'published_at',
        'api_source',
        'original_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug'; // Use slug for routes like /articles/{slug}
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category');
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'article_author');
    }
}

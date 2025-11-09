<?php

namespace App\Dtos;

use Illuminate\Support\Carbon;

/**
 * A standardized Data Transfer Object to hold article data
 * from various API sources before it's saved to the database.
 */
readonly class ArticleData
{
    public function __construct(
        public string $apiSource,
        public ?string $originalId,
        public string $sourceName,
        public string $title,
        public string $url,
        public ?string $description,
        public ?string $imageUrl,
        public ?Carbon $publishedAt,
        public ?string $authorName = null,
        public ?string $categoryName = null,
        public ?string $content = null,
    ) {
    }
}

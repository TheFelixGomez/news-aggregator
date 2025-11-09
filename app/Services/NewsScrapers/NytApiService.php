<?php

namespace App\Services\NewsScrapers;

use App\Dtos\ArticleData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NytApiService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.nyt.key');
        if (!$this->apiKey) {
            Log::error('New York Times API key is not set.');
        }
    }

    /**
     * @return Collection<ArticleData>
     */
    public function fetchArticles(): Collection
    {
        if (!$this->apiKey) {
            return collect();
        }

        try {
            // Using the "Top Stories" API as an example
            $response = Http::get('https://api.nytimes.com/svc/topstories/v2/home.json', [
                'api-key' => $this->apiKey,
            ]);

            if ($response->failed()) {
                Log::error('NYT API request failed', $response->json());
                return collect();
            }

            $articles = $response->json()['results'] ?? [];

            return collect($articles)->map(function ($article) {
                // NYT images are an array of objects, get the best one
                $imageUrl = null;
                if (!empty($article['multimedia'][0])) {
                    $imageUrl = $article['multimedia'][0]['url'];
                }

                // Map NYT's structure to our DTO
                return new ArticleData(
                    apiSource: 'nyt',
                    originalId: $article['uri'], // NYT uses 'uri' as a unique ID
                    sourceName: 'The New York Times',
                    title: $article['title'] ?? 'No Title',
                    url: $article['url'],
                    description: $article['abstract'],
                    imageUrl: $imageUrl,
                    publishedAt: Carbon::parse($article['published_date']),
                    authorName: $article['byline'] ? str_replace('By ', '', $article['byline']) : null,
                    categoryName: $article['section'] ?? null,
                    content: $article['abstract'] ?? null
                );
            })->unique('url');

        } catch (\Exception $e) {
            Log::error('Failed to fetch from NYT API', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}

<?php

namespace App\Services\NewsScrapers;

use App\Dtos\ArticleData;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        if (!$this->apiKey) {
            Log::error('The Guardian API key is not set.');
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
            $response = Http::get('https://content.guardianapis.com/search', [
                'api-key' => $this->apiKey,
                'show-fields' => 'headline,trailText,thumbnail,byline,body',
                'page-size' => 50,
            ]);

            if ($response->failed()) {
                Log::error('The Guardian API request failed', $response->json());
                return collect();
            }

            $articles = $response->json()['response']['results'] ?? [];

            return collect($articles)->map(function ($article) {
                return new ArticleData(
                    apiSource: 'guardian',
                    originalId: $article['id'],
                    sourceName: 'The Guardian',
                    title: $article['fields']['headline'] ?? 'No Title',
                    url: $article['webUrl'],
                    description: $article['fields']['trailText'],
                    imageUrl: $article['fields']['thumbnail'],
                    publishedAt: Carbon::parse($article['webPublicationDate']),
                    authorName: $article['fields']['byline'] ?? null,
                    categoryName: $article['sectionName'] ?? null,
                    content: $article['fields']['body'] ?? null,
                );
            })->unique('url');

        } catch (\Exception $e) {
            Log::error('Failed to fetch from The Guardian API', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}

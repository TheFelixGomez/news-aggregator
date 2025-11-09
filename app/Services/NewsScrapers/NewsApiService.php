<?php

namespace App\Services\NewsScrapers;

use App\Dtos\ArticleData;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        if (!$this->apiKey) {
            Log::error('NewsAPI key is not set.');
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
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'apiKey' => $this->apiKey,
                'country' => 'us',
                'pageSize' => 50,
            ]);

            if ($response->failed()) {
                Log::error('NewsAPI request failed', $response->json());
                return collect();
            }

            $articles = $response->json()['articles'] ?? [];

            return collect($articles)->map(function ($article) {
                return new ArticleData(
                    apiSource: 'newsapi',
                    originalId: null,
                    sourceName: $article['source']['name'] ?? 'Unknown Source',
                    title: $article['title'] ?? 'No Title',
                    url: $article['url'],
                    description: $article['description'],
                    imageUrl: $article['urlToImage'],
                    publishedAt: Carbon::parse($article['publishedAt']),
                    authorName: $article['author'],
                    categoryName: null, // NewsAPI 'top-headlines' doesn't provide a reliable category
                    content: $article['content'],
                );
            })->unique('url'); // Filter out duplicates from the same API call

        } catch (Exception $e) {
            Log::error('Failed to fetch from NewsAPI', ['error' => $e->getMessage()]);
            return collect();
        }
    }
}

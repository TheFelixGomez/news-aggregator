<?php

namespace App\Console\Commands;

use App\Dtos\ArticleData;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\NewsScrapers\GuardianApiService;
use App\Services\NewsScrapers\NewsApiService;
use App\Services\NewsScrapers\NytApiService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class FetchNewsArticles extends Command
{
    protected $signature = 'news:fetch-articles';
    protected $description = 'Fetches and stores news articles from all configured APIs';

    public function __construct(
        private readonly NewsApiService $newsApi,
        private readonly GuardianApiService $guardianApi,
        private readonly NytApiService $nytApi
    ) {
        parent::__construct();
    }

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        $this->info('Starting to fetch articles...');
        $services = [$this->newsApi, $this->guardianApi, $this->nytApi];
        $allArticles = collect();

        // 1. Fetch from all services
        foreach ($services as $service) {
            $this->info('Fetching from ' . class_basename($service) . '...');
            try {
                $allArticles = $allArticles->merge($service->fetchArticles());
            } catch (Exception $e) {
                $this->error('Failed to fetch from ' . class_basename($service) . ': ' . $e->getMessage());
            }
        }

        $this->info("Fetched a total of {$allArticles->count()} articles. Processing...");

        // 2. Process and save to database
        $bar = $this->output->createProgressBar($allArticles->count());
        $bar->start();

        foreach ($allArticles as $articleData) {
            if (!$articleData instanceof ArticleData || !$articleData->publishedAt) {
                $bar->advance();
                continue;
            }

            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($articleData) {
                // Find or create the Source
                $source = Source::firstOrCreate(
                    ['slug' => Str::slug($articleData->sourceName)],
                    ['name' => $articleData->sourceName, 'api_source_id' => $articleData->originalId]
                );

                // Find or create the Author
                $author = null;
                if ($articleData->authorName) {
                    $author = Author::firstOrCreate(
                        ['slug' => Str::slug($articleData->authorName)],
                        ['name' => $articleData->authorName]
                    );
                }

                // Find or create the Category
                $category = null;
                if ($articleData->categoryName) {
                    $category = Category::firstOrCreate(
                        ['slug' => Str::slug($articleData->categoryName)],
                        ['name' => $articleData->categoryName]
                    );
                }

                // Determine the unique attributes for this article.
                // If original_id is present (from NYT, Guardian), use it.
                // If not (from NewsAPI), fall back to the URL as the unique key.
                $uniqueAttributes = $articleData->originalId
                    ? ['api_source' => $articleData->apiSource, 'original_id' => $articleData->originalId]
                    : ['url' => $articleData->url];

                $articleValues = [
                    'source_id' => $source->id,
                    'title' => $articleData->title,
                    'slug' => Str::slug($articleData->title) . '-' . uniqid(),
                    'description' => $articleData->description,
                    'image_url' => $articleData->imageUrl,
                    'published_at' => $articleData->publishedAt,
                    'api_source' => $articleData->apiSource,
                    'original_id' => $articleData->originalId,
                    'url' => $articleData->url, // Ensure URL is always included
                    'content' => $articleData->content,
                ];

                // Create or update the Article
                $article = Article::updateOrCreate($uniqueAttributes, $articleValues);

                // Attach relationships
                if ($author) {
                    $article->authors()->syncWithoutDetaching([$author->id]);
                }
                if ($category) {
                    $article->categories()->syncWithoutDetaching([$category->id]);
                }
            });

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nArticle processing complete!");
        return 0;
    }
}

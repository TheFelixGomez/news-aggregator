<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        // Load preferences
        $user->load(['preferredSources', 'preferredCategories', 'preferredAuthors']);

        $preferredSourceIds = $user->preferredSources->pluck('id');
        $preferredCategoryIds = $user->preferredCategories->pluck('id');
        $preferredAuthorIds = $user->preferredAuthors->pluck('id');

        // Build the personalized query
        $articlesQuery = Article::query()
            ->with(['source', 'categories', 'authors'])
            ->latest('published_at');

        // Apply filters if the user has set any preferences
        $articlesQuery->when($preferredSourceIds->isNotEmpty(), function ($q) use ($preferredSourceIds) {
            $q->whereIn('source_id', $preferredSourceIds);
        });

        $articlesQuery->when($preferredCategoryIds->isNotEmpty(), function ($q) use ($preferredCategoryIds) {
            $q->whereHas('categories', function ($sq) use ($preferredCategoryIds) {
                $sq->whereIn('categories.id', $preferredCategoryIds);
            });
        });

        $articlesQuery->when($preferredAuthorIds->isNotEmpty(), function ($q) use ($preferredAuthorIds) {
            $q->whereHas('authors', function ($sq) use ($preferredAuthorIds) {
                $sq->whereIn('authors.id', $preferredAuthorIds);
            });
        });

        // If no preferences are set, show a generic feed.
        // You could also add logic here to *boost* preferred items.
        // For this example, if preferences are set, we *only* show items matching them.
        // If no preferences are set, the query will just return the latest articles.

        $articles = $articlesQuery->paginate(20);

        return Inertia::render('Dashboard', [
            'articles' => $articles,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Article::query()
            ->with(['source', 'categories', 'authors'])
            ->latest('published_at');

        // if at least one filter is applied, we won't apply user preferences
        $filtersApplied = false;

        // --- Apply Filters by Request ---
        // Search Keyword
        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%');
            $filtersApplied = true;
        }

        // Filter by Source
        if ($request->filled('source')) {
            $query->whereHas('source', fn($q) => $q->where('slug', $request->source));
            $filtersApplied = true;
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->category));
            $filtersApplied = true;
        }

        // Filter by Date
        if ($request->filled('date_from')) {
            $query->where('published_at', '>=', $request->date_from);
            $filtersApplied = true;
        }
        if ($request->filled('date_to')) {
            $query->where('published_at', '<=', $request->date_to);
            $filtersApplied = true;
        }

        // --- Apply User Preferences if no specific filters applied ---
        if (!$filtersApplied) {
            $user = $request->user();
            $user->load(['preferredSources', 'preferredCategories', 'preferredAuthors']);

            $preferredSourceIds = $user->preferredSources->pluck('id');
            $preferredCategoryIds = $user->preferredCategories->pluck('id');
            $preferredAuthorIds = $user->preferredAuthors->pluck('id');

            // Apply preferred sources *only if* no specific source is being filtered via the request
            $query->when($preferredSourceIds->isNotEmpty() && !$request->filled('source'), function ($q) use ($preferredSourceIds) {
                $q->whereIn('source_id', $preferredSourceIds);
            });

            // Apply preferred categories *only if* no specific category is being filtered via the request
            $query->when($preferredCategoryIds->isNotEmpty() && !$request->filled('category'), function ($q) use ($preferredCategoryIds) {
                $q->whereHas('categories', function ($sq) use ($preferredCategoryIds) {
                    $sq->whereIn('categories.id', $preferredCategoryIds);
                });
            });

            // Apply preferred authors (we don't have an explicit author filter in the request, so just apply it)
            $query->when($preferredAuthorIds->isNotEmpty(), function ($q) use ($preferredAuthorIds) {
                $q->whereHas('authors', function ($sq) use ($preferredAuthorIds) {
                    $sq->whereIn('authors.id', $preferredAuthorIds);
                });
            });
        }

        return Inertia::render('articles/index', [
            'articles' => $query->paginate(30)->withQueryString(),
            'filters' => $request->only(['q', 'source', 'category', 'date_from', 'date_to']),
            'sources' => fn () => Source::all(['name', 'slug']),
            'categories'=> fn () => Category::all(['name', 'slug']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): Response
    {
        $article->load(['source', 'categories', 'authors']);

        return Inertia::render('articles/show', [
            'article' => $article,
        ]);
    }
}

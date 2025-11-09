<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PreferenceController extends Controller
{
    /**
     * Display the user's preference settings screen.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $user->load(['preferences', 'preferredSources', 'preferredCategories', 'preferredAuthors']);

        return Inertia::render('preferences', [
            'allSources' => fn () => Source::all(['id', 'name']),
            'allCategories' => fn () => Category::all(['id', 'name']),
            'allAuthors' => fn () => Author::all(['id', 'name']), // May need to paginate this later
            'userPreferences' => $user->preferences,
            'userPreferredSourceIds' => fn () => $user->preferredSources->pluck('id'),
            'userPreferredCategoryIds' => fn () => $user->preferredCategories->pluck('id'),
            'userPreferredAuthorIds' => fn () => $user->preferredAuthors->pluck('id'),
        ]);
    }

    /**
     * Update the user's preference settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validate the incoming data
        $validated = $request->validate([
            'sources' => ['array'],
            'sources.*' => ['integer', 'exists:sources,id'],
            'categories' => ['array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'authors' => ['array'],
            'authors.*' => ['integer', 'exists:authors,id'],
        ]);

        // Sync many-to-many preferences
        if ($request->has('sources')) {
            $user->preferredSources()->sync($validated['sources']);
        }
        if ($request->has('categories')) {
            $user->preferredCategories()->sync($validated['categories']);
        }
        if ($request->has('authors')) {
            $user->preferredAuthors()->sync($validated['authors']);
        }

        return Redirect::route('news.preferences.edit')->with('success', 'Preferences updated.');
    }
}

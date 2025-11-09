import { MultiSelectCheckbox } from '@/components/multi-select-checkbox';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import news from '@/routes/news';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function ProfilePreferencesPage({
    allSources,
    allCategories,
    allAuthors,
    userPreferences,
    userPreferredSourceIds,
    userPreferredCategoryIds,
    userPreferredAuthorIds,
    errors,
}) {
    const { data, setData, put, processing, recentlySuccessful } = useForm({
        sources: userPreferredSourceIds || [],
        categories: userPreferredCategoryIds || [],
        authors: userPreferredAuthorIds || [],
    });

    const [activeTab, setActiveTab] = useState('sources');

    function submit(e) {
        e.preventDefault();
        put(news.preferences.update().url, {
            preserveScroll: true,
        });
    }

    function toggleId(key, id) {
        setData(
            key,
            data[key].includes(id)
                ? data[key].filter((i) => i !== id)
                : [...data[key], id],
        );
    }

    return (
        <AppLayout>
            <Head title="Your Preferences" />
            <div className="p-4">
                <h1 className="text-3xl font-bold tracking-tight">
                    Your Preferences
                </h1>
                <p className="text-muted-foreground">
                    Customize your news feed and app settings.
                </p>
            </div>

            <form onSubmit={submit}>
                <div className="grid gap-6 md:grid-cols-12">
                    <div className="md:col-span-3">
                        <nav className="flex flex-col space-y-1">
                            <Button
                                type="button"
                                variant={
                                    activeTab === 'sources'
                                        ? 'secondary'
                                        : 'ghost'
                                }
                                onClick={() => setActiveTab('sources')}
                                className="justify-start"
                            >
                                Sources
                            </Button>
                            <Button
                                type="button"
                                variant={
                                    activeTab === 'categories'
                                        ? 'secondary'
                                        : 'ghost'
                                }
                                onClick={() => setActiveTab('categories')}
                                className="justify-start"
                            >
                                Categories
                            </Button>
                            <Button
                                type="button"
                                variant={
                                    activeTab === 'authors'
                                        ? 'secondary'
                                        : 'ghost'
                                }
                                onClick={() => setActiveTab('authors')}
                                className="justify-start"
                            >
                                Authors
                            </Button>
                        </nav>
                    </div>
                    <div className="md:col-span-9">
                        <Card>
                            {/* Sources Tab */}
                            {activeTab === 'sources' && (
                                <>
                                    <CardHeader>
                                        <CardTitle>Preferred Sources</CardTitle>
                                        <CardDescription>
                                            Select sources to see in your feed.
                                            If none are selected, all sources
                                            will be shown.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="grid grid-cols-2 gap-4 md:grid-cols-3">
                                        {allSources.map((source) => (
                                            <MultiSelectCheckbox
                                                key={source.id}
                                                item={source}
                                                selectedIds={data.sources}
                                                onToggle={(id) =>
                                                    toggleId('sources', id)
                                                }
                                            />
                                        ))}
                                    </CardContent>
                                </>
                            )}

                            {/* Categories Tab */}
                            {activeTab === 'categories' && (
                                <>
                                    <CardHeader>
                                        <CardTitle>
                                            Preferred Categories
                                        </CardTitle>
                                        <CardDescription>
                                            Select categories to see in your
                                            feed.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="grid grid-cols-2 gap-4 md:grid-cols-3">
                                        {allCategories.map((cat) => (
                                            <MultiSelectCheckbox
                                                key={cat.id}
                                                item={cat}
                                                selectedIds={data.categories}
                                                onToggle={(id) =>
                                                    toggleId('categories', id)
                                                }
                                            />
                                        ))}
                                    </CardContent>
                                </>
                            )}

                            {/* Authors Tab */}
                            {activeTab === 'authors' && (
                                <>
                                    <CardHeader>
                                        <CardTitle>Preferred Authors</CardTitle>
                                        <CardDescription>
                                            Select authors to follow in your
                                            feed.
                                        </CardDescription>
                                    </CardHeader>
                                    <CardContent className="grid grid-cols-2 gap-4 md:grid-cols-3">
                                        {allAuthors.map((author) => (
                                            <MultiSelectCheckbox
                                                key={author.id}
                                                item={author}
                                                selectedIds={data.authors}
                                                onToggle={(id) =>
                                                    toggleId('authors', id)
                                                }
                                            />
                                        ))}
                                    </CardContent>
                                </>
                            )}

                            <CardFooter className="border-t px-6 py-4">
                                <Button type="submit" disabled={processing}>
                                    Save Preferences
                                </Button>
                                {recentlySuccessful && (
                                    <p className="ml-4 text-sm text-muted-foreground">
                                        Saved.
                                    </p>
                                )}
                            </CardFooter>
                        </Card>
                    </div>
                </div>
            </form>
        </AppLayout>
    );
}

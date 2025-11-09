import { ArticleCard } from '@/components/article-card';
import { Pagination } from '@/components/pagination';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import * as articlesRoute from '@/routes/articles';
import { Head, useForm } from '@inertiajs/react';
import { FilterIcon, SearchIcon } from 'lucide-react';

export default function ArticlesIndexPage({
    articles,
    filters,
    sources,
    categories,
}) {
    const { data, setData, get, processing } = useForm({
        q: filters.q || '',
        source: filters.source || '',
        category: filters.category || '',
        date_from: filters.date_from || '',
        date_to: filters.date_to || '',
    });

    function submit(e) {
        e.preventDefault();
        get(articlesRoute.index().url, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    return (
        <AppLayout>
            <Head title="Search Articles" />

            <div className="p-4">
                <h1 className="text-3xl font-bold tracking-tight">
                    Search Articles
                </h1>
                <p className="text-muted-foreground">
                    Filter and search all articles from our database.
                </p>
            </div>

            <Card className="mb-6">
                <form onSubmit={submit}>
                    <CardContent className="grid grid-cols-1 gap-4 p-6 md:grid-cols-4">
                        <div className="md:col-span-2">
                            <Label htmlFor="q">Keyword</Label>
                            <div className="relative">
                                <Input
                                    id="q"
                                    type="search"
                                    placeholder="Search by title or description..."
                                    value={data.q}
                                    onChange={(e) =>
                                        setData('q', e.target.value)
                                    }
                                    className="pl-9"
                                />
                                <SearchIcon className="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground" />
                            </div>
                        </div>
                        <div>
                            <Label htmlFor="source">Source</Label>
                            <Select
                                value={data.source}
                                onValueChange={(value) => setData('source', value === 'all' ? '' : value)}
                            >
                                <SelectTrigger id="source">
                                    <SelectValue placeholder="All Sources" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">
                                        All Sources
                                    </SelectItem>
                                    {sources.map((source) => (
                                        <SelectItem
                                            key={source.slug}
                                            value={source.slug}
                                        >
                                            {source.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        <div>
                            <Label htmlFor="category">Category</Label>
                            <Select
                                value={data.category}
                                onValueChange={(value) => setData('category', value === 'all' ? '' : value)}
                            >
                                <SelectTrigger id="category">
                                    <SelectValue placeholder="All Categories" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">
                                        All Categories
                                    </SelectItem>
                                    {categories.map((cat) => (
                                        <SelectItem
                                            key={cat.slug}
                                            value={cat.slug}
                                        >
                                            {cat.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                        <div className="md:col-span-3 lg:col-span-1">
                            <Label htmlFor="date_from">Date From</Label>
                            <Input
                                id="date_from"
                                type="date"
                                value={data.date_from}
                                onChange={(e) => setData('date_from', e.target.value)}
                            />
                        </div>
                        <div className="md:col-span-3 lg:col-span-1">
                            <Label htmlFor="date_to">Date To</Label>
                            <Input
                                id="date_to"
                                type="date"
                                value={data.date_to}
                                onChange={(e) => setData('date_to', e.target.value)}
                            />
                        </div>
                    </CardContent>
                    <CardFooter className="border-t px-6 py-4">
                        <Button type="submit" disabled={processing}>
                            <FilterIcon className="mr-2 h-4 w-4" />
                            Filter
                        </Button>
                    </CardFooter>
                </form>
            </Card>

            {articles.data.length > 0 ? (
                <>
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {articles.data.map((article) => (
                            <ArticleCard key={article.id} article={article} />
                        ))}
                    </div>
                    <Pagination
                        links={articles.links}
                        currentPage={articles.current_page}
                        lastPage={articles.last_page}
                    />
                </>
            ) : (
                <Card className="flex flex-col items-center justify-center p-12">
                    <CardTitle>No articles found.</CardTitle>
                    <CardDescription className="mt-2">
                        Try adjusting your search filters.
                    </CardDescription>
                </Card>
            )}
        </AppLayout>
    );
}

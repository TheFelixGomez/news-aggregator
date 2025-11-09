import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Link } from '@inertiajs/react';
import articles from '@/routes/articles';

function formatArticleDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export function ArticleCard({ article }) {
    // Add fallbacks for potentially undefined nested data
    const sourceName = article?.source?.name || 'Unknown Source';
    const categories = Array.isArray(article?.categories)
        ? article.categories
        : [];
    const authors = Array.isArray(article?.authors) ? article.authors : [];
    const articleTitle = article?.title || 'Untitled Article';
    const articleImageUrl =
        article?.image_url ||
        'https://placehold.co/600x400/27272a/71717a?text=News';
    const articleDescription =
        article?.description || 'No description available.';
    const publishedAt = formatArticleDate(article?.published_at);
    const articleSlug = article?.slug || 'no-slug'; // Fallback for slug

    return (
        <Card className="flex flex-col overflow-hidden">
            <CardHeader>
                <div className="mb-2 flex flex-wrap gap-2">
                    <Badge variant="secondary">{sourceName}</Badge>
                    {categories.slice(0, 2).map((cat) => (
                        <Badge key={cat.slug} variant="outline">
                            {cat.name}
                        </Badge>
                    ))}
                </div>
                <CardTitle className="text-lg">
                    <Link
                        href={articles.show(articleSlug)}
                        className="hover:text-primary hover:underline"
                    >
                        {articleTitle}
                    </Link>
                    {/* --- END MODIFICATION --- */}
                </CardTitle>
            </CardHeader>
            <CardContent className="flex-grow">
                <img
                    src={articleImageUrl}
                    alt={articleTitle}
                    className="mb-4 aspect-video w-full rounded-md object-cover"
                    onError={(e) => {
                        // Fallback if the image link is broken
                        e.currentTarget.src =
                            'https://placehold.co/600x400/27272a/71717a?text=News';
                    }}
                />
                <p
                    className="text-sm text-muted-foreground"
                    dangerouslySetInnerHTML={{ __html: articleDescription }}
                />
            </CardContent>
            <CardFooter className="text-xs text-muted-foreground">
                <p>
                    {authors.length > 0
                        ? `${authors.map((a) => a.name).join(', ')} | `
                        : ''}
                    {publishedAt}
                </p>
            </CardFooter>
        </Card>
    );
}

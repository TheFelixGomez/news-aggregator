import AppLayout from '@/layouts/app-layout';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Head } from '@inertiajs/react';
import { Button } from '@headlessui/react';
import { ExternalLinkIcon } from 'lucide-react';

function formatArticleDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export default function ArticlesShowPage({ article }) {
    return (
        <AppLayout>
            <Head title={article.title} />
            <Card>
                <CardHeader>
                    <div className="mb-2 flex flex-wrap gap-2">
                        <Badge variant="secondary">{article.source.name}</Badge>
                        {article.categories.map((cat) => (
                            <Badge key={cat.slug} variant="outline">
                                {cat.name}
                            </Badge>
                        ))}
                    </div>
                    <CardTitle className="text-2xl">{article.title}</CardTitle>
                    <CardDescription>
                        By {article.authors.map(a => a.name).join(', ')} | {formatArticleDate(article.published_at)}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <img
                        src={article.image_url || 'https://placehold.co/800x400/27272a/71717a?text=News'}
                        alt={article.title}
                        className="mb-6 aspect-video w-full rounded-md object-cover"
                        onError={(e) => {
                            e.currentTarget.src = 'https://placehold.co/800x400/27272a/71717a?text=News';
                        }}
                    />
                    <div className="prose prose-sm dark:prose-invert max-w-none">
                        <p className="lead">{article.description}</p>
                        {/* The 'content' prop would be rendered here if it contains safe HTML */}
                        {/* <div dangerouslySetInnerHTML={{ __html: article.content }} /> */}
                        <p>
                            This is a placeholder for the full article content, which would be
                            rendered here.
                        </p>
                    </div>
                </CardContent>
                <CardFooter>
                    <Button as="a" href={article.url} target="_blank" rel="noopener noreferrer">
                        Read Full Article
                        <ExternalLinkIcon className="ml-2 h-4 w-4" />
                    </Button>
                </CardFooter>
            </Card>
        </AppLayout>
    );
}

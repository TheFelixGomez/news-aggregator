import { Button } from '@/components/ui/button';
import { ChevronLeftIcon, ChevronRightIcon } from 'lucide-react';
import { Link } from '@inertiajs/react';

export function Pagination({ links, currentPage, lastPage }) {
    // get prev and next links from the links array
    const prevLink = links.find((link) => link.label === '&laquo; Previous');
    const nextLink = links.find((link) => link.label === 'Next &raquo;');

    const paginationLinks = {
        prev: prevLink ? prevLink.url : null,
        next: nextLink ? nextLink.url : null,
    };

    return (
        <div className="mt-8 flex items-center justify-center space-x-2">
            <Button
                asChild
                variant="outline"
                size="sm"
                disabled={!paginationLinks.prev}
            >
                <Link href={paginationLinks.prev || '#'}>
                    <ChevronLeftIcon className="mr-1 h-4 w-4" />
                    Previous
                </Link>
            </Button>

            <span className="text-sm text-muted-foreground">
                Page {currentPage} of {lastPage}
            </span>

            <Button
                asChild
                variant="outline"
                size="sm"
                disabled={!paginationLinks.next}
            >
                <Link href={paginationLinks.next || '#'}>
                    Next
                    <ChevronRightIcon className="ml-1 h-4 w-4" />
                </Link>
            </Button>
        </div>
    );
}

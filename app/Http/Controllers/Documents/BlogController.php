<?php

namespace App\Http\Controllers\Documents;

use App\Models\Document;
use Inertia\Response;

class BlogController extends BaseDocumentController
{
    protected function getComponent(): string
    {
        return 'Documents/Blog';
    }

    public function show(Document $document): Response
    {
        $document->load(['site', 'author', 'children' => function ($query) {
            $query->published()->orderBy('published_at', 'desc');
        }]);

        return parent::show($document)->with([
            'relatedPosts' => $this->getRelatedPosts($document),
        ]);
    }

    protected function getRelatedPosts(Document $document): array
    {
        return Document::query()
            ->forSite($document->site_id)
            ->published()
            ->where('type', 'page')
            ->where('id', '!=', $document->id)
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn ($doc) => [
                'id' => $doc->id,
                'title' => $doc->title,
                'slug' => $doc->slug,
                'published_at' => $doc->published_at,
            ])
            ->toArray();
    }
}

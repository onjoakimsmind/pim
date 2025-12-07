<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Inertia\Inertia;
use Inertia\Response;

abstract class BaseDocumentController extends Controller
{
    abstract protected function getComponent(): string;

    public function show(Document $document): Response
    {
        $document->load(['site', 'author', 'children' => function ($query) {
            $query->published()->orderBy('sort_order');
        }]);

        return Inertia::render($this->getComponent(), [
            'document' => $document,
            'breadcrumbs' => $this->getBreadcrumbs($document),
        ]);
    }

    protected function getBreadcrumbs(Document $document): array
    {
        $breadcrumbs = [];
        $current = $document;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'title' => $current->title,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }

    protected function getAdditionalData(Document $document): array
    {
        return [];
    }
}

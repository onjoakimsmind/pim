<?php

namespace App\Http\Controllers\Documents;

use App\Models\Document;
use Inertia\Response;

class ProductController extends BaseDocumentController
{
    protected function getComponent(): string
    {
        return 'Documents/Product';
    }

    public function show(Document $document): Response
    {
        $document->load(['site', 'author']);

        $productData = $document->meta ?? [];

        return parent::show($document)->with([
            'specifications' => $productData['specifications'] ?? [],
            'variants' => $this->getProductVariants($document),
            'relatedProducts' => $this->getRelatedProducts($document),
        ]);
    }

    protected function getProductVariants(Document $document): array
    {
        return $document->children()
            ->where('type', 'page')
            ->published()
            ->get()
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'title' => $variant->title,
                'slug' => $variant->slug,
                'meta' => $variant->meta,
            ])
            ->toArray();
    }

    protected function getRelatedProducts(Document $document): array
    {
        if (!$document->parent_id) {
            return [];
        }

        return Document::query()
            ->where('parent_id', $document->parent_id)
            ->where('id', '!=', $document->id)
            ->published()
            ->limit(4)
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
            ])
            ->toArray();
    }
}

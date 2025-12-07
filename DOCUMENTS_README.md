# Multi-Tenant Document System with Dynamic Controllers

## Overview

This PIM system features a flexible multi-tenant document management system where each document can specify its own controller, allowing different Vue components to be rendered based on the document type.

## Architecture

### Database Schema

#### Sites Table
- `id` - Primary key
- `name` - Site name
- `domain` - Unique domain for the site
- `locale` - Default locale (e.g., 'en', 'es')
- `is_active` - Boolean flag

#### Documents Table
- `id` - Primary key
- `site_id` - Foreign key to sites (multi-tenant isolation)
- `parent_id` - Self-referencing for hierarchical structure
- `author_id` - Foreign key to users
- `type` - Document type ('page', 'folder', 'link')
- `controller` - **Controller class name** (nullable)
- `key` - Optional unique key
- `title` - Document title
- `slug` - URL slug (unique per site + parent)
- `content` - Text content
- `meta` - JSON field for flexible attributes
- `sort_order` - Integer for ordering
- `published` - Boolean
- `published_at` - Timestamp
- `created_at`, `updated_at`, `deleted_at` (soft deletes)

## Document Controllers

### How It Works

1. Each document has an optional `controller` field
2. The `DocumentResolver` service maps controller names to classes
3. When a document is viewed, the resolver determines which controller to use
4. Each controller extends `BaseDocumentController` and specifies a Vue component
5. Different controllers can load different data and render different views

### Available Controllers

#### DefaultController
- **Component**: `Documents/Default`
- **Use Case**: Standard pages with hierarchical content
- **Features**: Breadcrumbs, child documents listing

#### BlogController
- **Component**: `Documents/Blog`
- **Use Case**: Blog posts and articles
- **Features**: 
  - Enhanced published date display
  - Related posts section
  - Author information with icons

#### ProductController
- **Component**: `Documents/Product`
- **Use Case**: PIM product pages
- **Features**:
  - Product specifications from meta field
  - Product variants (child documents)
  - Related products (sibling documents)
  - Sidebar with product metadata (SKU, price, availability)

### Creating a Custom Controller

1. **Create the Controller Class**:
```php
namespace App\Http\Controllers\Documents;

use App\Models\Document;
use Inertia\Response;

class MyCustomController extends BaseDocumentController
{
    protected function getComponent(): string
    {
        return 'Documents/MyCustom';
    }

    public function show(Document $document): Response
    {
        $document->load(['site', 'author']);

        return parent::show($document)->with([
            'customData' => $this->getCustomData($document),
        ]);
    }

    protected function getCustomData(Document $document): array
    {
        // Your custom logic here
        return [];
    }
}
```

2. **Register in DocumentResolver**:
```php
// In app/Services/DocumentResolver.php
protected array $controllerMap = [
    'MyCustomController' => \App\Http\Controllers\Documents\MyCustomController::class,
];
```

3. **Create Vue Component**:
```vue
<!-- resources/js/Pages/Documents/MyCustom.vue -->
<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

const props = defineProps<{
    document: Document
    breadcrumbs: Breadcrumb[]
    customData: any
}>()
</script>

<template>
    <div>
        <Head :title="document.title" />
        <!-- Your custom template -->
    </div>
</template>
```

4. **Set Controller on Document**:
```php
$document->update(['controller' => 'MyCustomController']);
```

## Usage Examples

### Creating Documents with Controllers

```php
use App\Models\Site;
use App\Models\Document;
use App\Models\User;

$site = Site::factory()->create();
$user = User::first();

// Default page
$page = Document::create([
    'site_id' => $site->id,
    'author_id' => $user->id,
    'title' => 'About Us',
    'slug' => 'about',
    'controller' => 'DefaultController', // or null
    'published' => true,
]);

// Blog post
$post = Document::create([
    'site_id' => $site->id,
    'author_id' => $user->id,
    'title' => 'My First Post',
    'slug' => 'my-first-post',
    'controller' => 'BlogController',
    'content' => 'Post content...',
    'published' => true,
    'published_at' => now(),
]);

// Product page
$product = Document::create([
    'site_id' => $site->id,
    'author_id' => $user->id,
    'title' => 'Premium Widget',
    'slug' => 'premium-widget',
    'controller' => 'ProductController',
    'meta' => [
        'sku' => 'WIDGET-001',
        'price' => '$99.99',
        'availability' => 'In Stock',
        'specifications' => [
            'Weight' => '500g',
            'Dimensions' => '10x10x5 cm',
            'Material' => 'Aluminum',
        ],
    ],
    'published' => true,
]);
```

### Query Scopes

```php
// Get documents for a specific site
$documents = Document::forSite($siteId)->get();

// Get published documents
$published = Document::published()->get();

// Get root level documents (no parent)
$rootDocs = Document::rootLevel()->get();

// Combine scopes
$sitePublished = Document::forSite($siteId)
    ->published()
    ->rootLevel()
    ->orderBy('sort_order')
    ->get();
```

## Multi-Tenancy

Documents are automatically isolated by site:
- Unique slugs are enforced per site + parent combination
- Same slug can exist on different sites
- Query documents by site using `forSite($siteId)` scope
- All relationships maintain site isolation

## Extending the System

### Adding Custom Meta Fields

Store structured data in the `meta` JSON field:

```php
$document->meta = [
    'seo' => [
        'title' => 'SEO Title',
        'description' => 'Meta description',
        'keywords' => ['tag1', 'tag2'],
    ],
    'custom_field' => 'value',
];
$document->save();
```

### Dynamic Controller Registration

Register controllers at runtime:

```php
$resolver = app(\App\Services\DocumentResolver::class);
$resolver->registerController('CustomController', CustomController::class);
```

## Testing

All document functionality is covered by comprehensive tests:
- Multi-tenant isolation
- Hierarchical relationships
- Controller resolution
- Unique constraints
- Soft deletes
- Query scopes

Run tests: `lando artisan test --filter=DocumentTest`

## Routes

- `GET /documents/{document}` - View a document (resolved to appropriate controller)

The `DocumentController` acts as a dispatcher, using `DocumentResolver` to determine the correct controller based on the document's `controller` field.

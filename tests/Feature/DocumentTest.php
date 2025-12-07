<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    public function test_document_belongs_to_site(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
        ]);

        $this->assertInstanceOf(Site::class, $document->site);
        $this->assertEquals($site->id, $document->site->id);
    }

    public function test_site_has_many_documents(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        Document::factory()->count(3)->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
        ]);

        $this->assertCount(3, $site->documents);
    }

    public function test_documents_are_isolated_per_site(): void
    {
        $site1 = Site::factory()->create(['name' => 'Site 1']);
        $site2 = Site::factory()->create(['name' => 'Site 2']);
        $user = User::factory()->create();

        Document::factory()->count(3)->create([
            'site_id' => $site1->id,
            'author_id' => $user->id,
        ]);

        Document::factory()->count(5)->create([
            'site_id' => $site2->id,
            'author_id' => $user->id,
        ]);

        $this->assertCount(3, $site1->documents);
        $this->assertCount(5, $site2->documents);
        $this->assertEquals(8, Document::count());
    }

    public function test_document_can_have_parent_and_children(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        
        $parent = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
            'parent_id' => null,
        ]);

        $child = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
            'parent_id' => $parent->id,
        ]);

        $this->assertInstanceOf(Document::class, $child->parent);
        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertCount(1, $parent->children);
    }

    public function test_slug_must_be_unique_per_site_and_parent(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        $parent = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
        ]);

        Document::create([
            'site_id' => $site->id,
            'author_id' => $user->id,
            'slug' => 'test-page',
            'title' => 'Test Page',
            'parent_id' => $parent->id,
        ]);

        try {
            Document::create([
                'site_id' => $site->id,
                'author_id' => $user->id,
                'slug' => 'test-page',
                'title' => 'Test Page 2',
                'parent_id' => $parent->id,
            ]);
            $this->fail('Expected unique constraint violation did not occur');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertTrue(
                str_contains($e->getMessage(), 'Duplicate entry') || 
                str_contains($e->getMessage(), 'UNIQUE constraint failed')
            );
        }
    }

    public function test_same_slug_allowed_on_different_sites(): void
    {
        $site1 = Site::factory()->create();
        $site2 = Site::factory()->create();
        $user = User::factory()->create();

        $doc1 = Document::factory()->create([
            'site_id' => $site1->id,
            'author_id' => $user->id,
            'slug' => 'same-slug',
        ]);

        $doc2 = Document::factory()->create([
            'site_id' => $site2->id,
            'author_id' => $user->id,
            'slug' => 'same-slug',
        ]);

        $this->assertEquals('same-slug', $doc1->slug);
        $this->assertEquals('same-slug', $doc2->slug);
        $this->assertNotEquals($doc1->site_id, $doc2->site_id);
    }

    public function test_document_soft_deletes(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
        ]);

        $document->delete();

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
        $this->assertNotNull($document->fresh()->deleted_at);
    }

    public function test_document_scopes_work_correctly(): void
    {
        $site1 = Site::factory()->create();
        $site2 = Site::factory()->create();
        $user = User::factory()->create();

        Document::factory()->count(2)->published()->create([
            'site_id' => $site1->id,
            'author_id' => $user->id,
            'parent_id' => null,
        ]);

        Document::factory()->count(3)->draft()->create([
            'site_id' => $site1->id,
            'author_id' => $user->id,
            'parent_id' => null,
        ]);

        Document::factory()->count(4)->published()->create([
            'site_id' => $site2->id,
            'author_id' => $user->id,
            'parent_id' => null,
        ]);

        $this->assertCount(5, Document::forSite($site1->id)->get());
        $this->assertCount(2, Document::forSite($site1->id)->published()->get());
        $this->assertCount(5, Document::forSite($site1->id)->rootLevel()->get());
        $this->assertCount(4, Document::forSite($site2->id)->published()->get());
    }

    public function test_document_controller_field_can_be_set(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        
        $document = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
            'controller' => 'BlogController',
        ]);

        $this->assertEquals('BlogController', $document->controller);
    }

    public function test_document_resolver_uses_default_when_no_controller(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
            'controller' => null,
        ]);

        $resolver = app(\App\Services\DocumentResolver::class);
        $controller = $resolver->resolveController($document);

        $this->assertInstanceOf(\App\Http\Controllers\Documents\DefaultController::class, $controller);
    }

    public function test_document_resolver_resolves_correct_controller(): void
    {
        $site = Site::factory()->create();
        $user = User::factory()->create();
        $document = Document::factory()->create([
            'site_id' => $site->id,
            'author_id' => $user->id,
            'controller' => 'BlogController',
        ]);

        $resolver = app(\App\Services\DocumentResolver::class);
        $controller = $resolver->resolveController($document);

        $this->assertInstanceOf(\App\Http\Controllers\Documents\BlogController::class, $controller);
    }
}

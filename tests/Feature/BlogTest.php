<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_lists_published_posts(): void
    {
        Post::create([
            'title' => 'Premier article',
            'slug' => 'premier-article',
            'excerpt' => 'Un résumé clair.',
            'is_published' => true,
        ]);

        Post::create([
            'title' => 'Brouillon secret',
            'slug' => 'brouillon-secret',
            'is_published' => false,
        ]);

        $response = $this->get('/blog');

        $response->assertOk()
            ->assertSee('Premier article')
            ->assertDontSee('Brouillon secret');
    }

    public function test_blog_article_page_renders_content_and_gallery(): void
    {
        $post = Post::create([
            'title' => 'Article complet',
            'slug' => 'article-complet',
            'excerpt' => 'Résumé de l\'article.',
            'content' => "Premier paragraphe du récit.\n\nDeuxième paragraphe, avec plus de détails.",
            'gallery' => ['storage/posts/g1.jpg', 'storage/posts/g2.jpg'],
            'is_published' => true,
        ]);

        $this->get('/blog/article-complet')
            ->assertOk()
            ->assertSee('Article complet')
            ->assertSee('Premier paragraphe du récit.')
            ->assertSee('Deuxième paragraphe, avec plus de détails.')
            ->assertSee('storage/posts/g1.jpg')
            ->assertSee('storage/posts/g2.jpg');
    }

    public function test_draft_article_returns_404(): void
    {
        Post::create([
            'title' => 'Brouillon',
            'slug' => 'brouillon',
            'is_published' => false,
        ]);

        $this->get('/blog/brouillon')->assertNotFound();
    }

    public function test_admin_can_create_post_and_public_page_reflects_it(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Nouvelle action sur le terrain',
            'excerpt' => 'Un court résumé.',
            'content' => "Paragraphe un.\n\nParagraphe deux.",
            'category' => 'Environnement',
            'is_published' => '1',
        ]);

        $post = Post::where('slug', 'nouvelle-action-sur-le-terrain')->first();
        $this->assertNotNull($post);

        $response->assertRedirect(route('admin.posts.edit', $post));

        $this->get('/blog')
            ->assertOk()
            ->assertSee('Nouvelle action sur le terrain');

        $this->get('/blog/nouvelle-action-sur-le-terrain')
            ->assertOk()
            ->assertSee('Paragraphe deux.');
    }

    public function test_admin_can_upload_main_image_and_gallery(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Article illustré',
            'is_published' => '1',
            'main_image_file' => UploadedFile::fake()->image('principale.jpg', 1200, 700),
            'gallery_files' => [
                UploadedFile::fake()->image('g1.jpg', 800, 600),
                UploadedFile::fake()->image('g2.png', 800, 600),
            ],
        ])->assertRedirect();

        $post = Post::where('slug', 'article-illustre')->first();
        $this->assertNotNull($post);

        $this->assertStringStartsWith('storage/posts/', $post->main_image);
        Storage::disk('public')->assertExists(substr($post->main_image, strlen('storage/')));

        $this->assertCount(2, $post->galleryImages());
        foreach ($post->galleryImages() as $image) {
            Storage::disk('public')->assertExists(substr($image, strlen('storage/')));
        }

        // The public article page must render main image and gallery.
        $this->get('/blog/article-illustre')
            ->assertOk()
            ->assertSee('posts/')
            ->assertSee('Galerie');
    }

    public function test_main_image_is_preserved_and_gallery_removable_per_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $post = Post::create([
            'title' => 'Article existant',
            'slug' => 'article-existant',
            'main_image' => 'storage/posts/keep.jpg',
            'gallery' => ['storage/posts/kill.jpg', 'storage/posts/keep2.jpg'],
            'is_published' => true,
        ]);
        Storage::disk('public')->put('posts/keep.jpg', 'x');
        Storage::disk('public')->put('posts/kill.jpg', 'x');
        Storage::disk('public')->put('posts/keep2.jpg', 'x');

        $this->actingAs($admin)->put(route('admin.posts.update', $post), [
            'title' => 'Article existant modifié',
            'is_published' => '1',
            'gallery_remove' => ['0' => '1'],
        ])->assertRedirect(route('admin.posts.edit', $post));

        $post->refresh();

        // Main image untouched, first gallery image removed, second kept.
        $this->assertSame('storage/posts/keep.jpg', $post->main_image);
        Storage::disk('public')->assertExists('posts/keep.jpg');
        $this->assertSame(['storage/posts/keep2.jpg'], $post->galleryImages());
        Storage::disk('public')->assertMissing('posts/kill.jpg');
    }

    public function test_deleting_post_removes_uploaded_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $post = Post::create([
            'title' => 'À supprimer',
            'slug' => 'a-supprimer',
            'main_image' => 'storage/posts/main.jpg',
            'gallery' => ['storage/posts/g.jpg'],
            'is_published' => true,
        ]);
        Storage::disk('public')->put('posts/main.jpg', 'x');
        Storage::disk('public')->put('posts/g.jpg', 'x');

        $this->actingAs($admin)->delete(route('admin.posts.destroy', $post))
            ->assertRedirect(route('admin.posts.index'));

        Storage::disk('public')->assertMissing('posts/main.jpg');
        Storage::disk('public')->assertMissing('posts/g.jpg');
        $this->assertDatabaseMissing('posts', ['slug' => 'a-supprimer']);
    }

    public function test_gallery_rejects_non_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Article refusé',
            'is_published' => '1',
            'gallery_files' => [
                UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertSessionHasErrors('gallery_files.0');
        $this->assertNull(Post::where('slug', 'article-refuse')->first());
    }

    public function test_admin_requires_authentication_for_posts(): void
    {
        $this->get('/admin/posts')->assertRedirect(route('admin.login'));
        $this->get('/admin/posts/create')->assertRedirect(route('admin.login'));
    }

    public function test_post_form_ships_filepond_uploader(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.posts.create'))
            ->assertOk()
            ->assertSee('filepond.min.js', false)
            ->assertSee('filepond-plugin-image-preview', false)
            ->assertSee('allowReorder', false)
            ->assertSee('name="gallery_files[]"', false);

        $post = Post::create([
            'title' => 'Avec galerie',
            'slug' => 'avec-galerie',
            'gallery' => ['storage/posts/existante.jpg'],
            'is_published' => true,
        ]);

        $this->actingAs($admin)->get(route('admin.posts.edit', $post))
            ->assertOk()
            ->assertSee('gallery_remove', false);
    }
}

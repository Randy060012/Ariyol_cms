<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_from_cms(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        foreach (['/', '/a-propos', '/programmes', '/realisations', '/contact'] as $url) {
            $response = $this->get($url);

            $response->assertStatus(200);
        }
    }

    public function test_custom_page_is_served_by_slug(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        Page::create([
            'title' => 'Galerie',
            'slug' => 'galerie',
            'page_key' => 'custom-galerie',
            'is_published' => true,
        ]);

        $this->get('/galerie')->assertOk()->assertSee('Galerie');
    }

    public function test_admin_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/pages')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_login_and_reach_dashboard(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->withoutExceptionHandling();

        $response = $this->post(route('admin.login.attempt'), [
            'email' => 'admin@afriyol.org',
            'password' => 'Afriyol2026!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->get('/admin')->assertOk()->assertSee('Tableau de bord');
    }

    public function test_admin_can_create_page_and_section(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $response = $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Galerie photos',
            'is_published' => '1',
        ]);

        $page = Page::where('slug', 'galerie-photos')->first();
        $this->assertNotNull($page);

        $response = $this->actingAs($admin)->post(
            route('admin.sections.store', $page),
            ['type' => 'cards']
        );

        $this->assertCount(1, $page->sections()->get());

        $this->actingAs($admin)->put(route('admin.sections.update', [$page, $page->sections->first()]), [
            'data' => [
                'kicker' => 'Nos images',
                'title' => 'Galerie',
                'items' => "Atelier jeunes | Tsévié 2026\nReboisement | Zéglé-Sagonou",
            ],
            'is_visible' => '1',
        ])->assertRedirect(route('admin.pages.edit', $page));

        $this->get('/galerie-photos')
            ->assertOk()
            ->assertSee('Atelier jeunes')
            ->assertSee('Reboisement');
    }

    public function test_admin_can_update_settings_and_footer_reflects_change(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'values' => [
                'brand_name' => 'AFRIYOL TEST',
                'contact_email' => 'bureau@afriyol.org',
            ],
            'types' => [],
            'groups' => [],
        ])->assertRedirect();

        $this->assertSame('bureau@afriyol.org', \App\Models\Setting::get('contact_email'));

        $this->get('/')->assertSee('bureau@afriyol.org');
    }

    public function test_admin_can_upload_image_to_section(): void
    {
        Storage::fake('public');
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'facts',
            'data' => ['title' => 'Impact'],
            'sort_order' => 99,
        ]);

        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Impact'],
                'is_visible' => '1',
                'data_file_image' => UploadedFile::fake()->image('action.jpg', 800, 600),
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();

        $this->assertStringStartsWith('storage/sections/', $section->field('image'));
        Storage::disk('public')->assertExists(substr($section->field('image'), strlen('storage/')));

        // The public page must render the uploaded image.
        $this->get('/')->assertSee('sections/');
    }

    public function test_section_image_is_preserved_when_editing_other_fields(): void
    {
        Storage::fake('public');
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'quote',
            'data' => [
                'quote' => 'Un mot inspirant.',
                'author' => 'Quelqu’un',
                'image' => 'storage/sections/existante.jpg',
            ],
            'sort_order' => 99,
        ]);
        Storage::disk('public')->put('sections/existante.jpg', 'fake-content');

        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => [
                    'quote' => 'Un mot modifié.',
                    'author' => 'Quelqu’un',
                ],
                'is_visible' => '1',
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();

        $this->assertSame('storage/sections/existante.jpg', $section->field('image'));
        Storage::disk('public')->assertExists('sections/existante.jpg');
    }

    public function test_admin_can_remove_section_image(): void
    {
        Storage::fake('public');
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'cta',
            'data' => [
                'title' => 'Agir',
                'image' => 'storage/sections/a-supprimer.jpg',
            ],
            'sort_order' => 99,
        ]);
        Storage::disk('public')->put('sections/a-supprimer.jpg', 'fake-content');

        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Agir'],
                'is_visible' => '1',
                'remove_image' => '1',
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();

        $this->assertNull($section->field('image'));
        Storage::disk('public')->assertMissing('sections/a-supprimer.jpg');

        $this->get('/')->assertDontSee('a-supprimer.jpg');
    }

    public function test_uploading_new_image_replaces_the_old_file(): void
    {
        Storage::fake('public');
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'cards',
            'data' => ['title' => 'Axes', 'image' => 'storage/sections/ancienne.jpg'],
            'sort_order' => 99,
        ]);
        Storage::disk('public')->put('sections/ancienne.jpg', 'fake-content');

        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Axes'],
                'is_visible' => '1',
                'data_file_image' => UploadedFile::fake()->image('nouvelle.png', 800, 600),
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();

        $this->assertNotSame('storage/sections/ancienne.jpg', $section->field('image'));
        Storage::disk('public')->assertMissing('sections/ancienne.jpg');
        $this->assertStringStartsWith('storage/sections/', $section->field('image'));
    }

    public function test_section_image_validation_rejects_non_images(): void
    {
        Storage::fake('public');
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'facts',
            'data' => ['title' => 'Impact'],
            'sort_order' => 99,
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Impact'],
                'is_visible' => '1',
                'data_file_image' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ]
        );

        $response->assertSessionHasErrors('data_file_image');
        $this->assertNull($section->fresh()->field('image'));
    }

    public function test_core_pages_cannot_be_deleted(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();
        $home = Page::where('page_key', 'home')->first();

        $this->actingAs($admin)->delete(route('admin.pages.destroy', $home));

        $this->assertModelExists($home);
    }
}

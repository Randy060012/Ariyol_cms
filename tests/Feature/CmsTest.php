<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use App\Services\PageRendererService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_from_cms(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach (['/', '/a-propos', '/programmes', '/realisations', '/contact'] as $url) {
            $response = $this->get($url);

            $response->assertStatus(200);
        }
    }

    public function test_custom_page_is_served_by_slug(): void
    {
        $this->seed(DatabaseSeeder::class);

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
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'values' => [
                'brand_name' => 'AFRIYOL TEST',
                'contact_email' => 'bureau@afriyol.org',
            ],
            'types' => [],
            'groups' => [],
        ])->assertRedirect();

        $this->assertSame('bureau@afriyol.org', Setting::get('contact_email'));

        $this->get('/')->assertSee('bureau@afriyol.org');
    }

    public function test_admin_can_upload_image_to_section(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
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
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();
        $home = Page::where('page_key', 'home')->first();

        $this->actingAs($admin)->delete(route('admin.pages.destroy', $home));

        $this->assertModelExists($home);
    }

    /* ------------------------------------------------------------------
     |  Photos : couverture de tous les types de sections
     * ------------------------------------------------------------------ */

    public function test_every_section_type_accepts_a_main_image_and_a_gallery(): void
    {
        foreach (array_keys(PageRendererService::SECTION_TYPES) as $type) {
            $fields = PageRendererService::sectionTypeFields($type);

            $this->assertArrayHasKey(
                'image',
                $fields,
                "Le type « {$type} » doit accepter une image principale."
            );
            $this->assertArrayHasKey(
                'images',
                $fields,
                "Le type « {$type} » doit accepter une galerie d'images."
            );

            // Les champs média sont résolus par le pipeline d'upload.
            $this->assertSame(PageRendererService::FIELD_IMAGE, $fields['image']['type']);
            $this->assertSame(PageRendererService::FIELD_GALLERY, $fields['images']['type']);
        }
    }

    public function test_admin_can_upload_images_on_a_partners_section(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->firstWhere('type', 'partners');

        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Nos partenaires'],
                'is_visible' => '1',
                'data_file_image' => UploadedFile::fake()->image('banniere.jpg', 800, 600),
                'data_file_images' => [
                    UploadedFile::fake()->image('action-1.jpg', 800, 600),
                    UploadedFile::fake()->image('action-2.jpg', 800, 600),
                ],
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();

        $this->assertStringStartsWith('storage/sections/', $section->field('image'));
        $this->assertCount(2, $section->gallery('images'));

        // L'image principale et la galerie sont bien rendues sur la page publique.
        $this->get('/')
            ->assertOk()
            ->assertSee($section->field('image'))
            ->assertSee($section->gallery('images')[0]);
    }

    public function test_admin_can_attach_a_photo_to_each_card(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'cards',
            'data' => [
                'title' => 'Nos actions',
                'items' => [
                    ['title' => 'Reboisement', 'description' => 'Zéglé-Sagonou'],
                    ['title' => 'Togo Propre', 'description' => 'Tsévié'],
                ],
            ],
            'sort_order' => 98,
            'is_visible' => true,
        ]);

        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => [
                    'title' => 'Nos actions',
                    'items' => "Reboisement | Zéglé-Sagonou\nTogo Propre | Tsévié",
                ],
                'is_visible' => '1',
                'data_item_image_0' => UploadedFile::fake()->image('carte-reboisement.jpg', 600, 400),
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();

        // La photo est rattachée à la première carte, la seconde n'en a pas.
        $items = $section->items();
        $this->assertStringStartsWith('storage/sections/', $items[0]['image']);
        $this->assertArrayNotHasKey('image', $items[1]);

        $this->get('/')
            ->assertOk()
            ->assertSee($items[0]['image'])
            ->assertDontSee($items[1]['title'].'.jpg');
    }

    public function test_card_photos_are_preserved_and_removable(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'cards',
            'data' => [
                'items' => [
                    ['title' => 'A', 'description' => '', 'image' => 'storage/sections/a.jpg'],
                    ['title' => 'B', 'description' => '', 'image' => 'storage/sections/b.jpg'],
                ],
            ],
            'sort_order' => 97,
            'is_visible' => true,
        ]);
        Storage::disk('public')->put('sections/a.jpg', 'x');
        Storage::disk('public')->put('sections/b.jpg', 'x');

        // Un autre champ est modifié : les photos restent en place.
        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Titre', 'items' => "A\nB"],
                'is_visible' => '1',
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();
        $this->assertSame([0 => 'storage/sections/a.jpg', 1 => 'storage/sections/b.jpg'], $section->itemImages());
        Storage::disk('public')->assertExists('sections/a.jpg');

        // La photo de la première carte est supprimée.
        $this->actingAs($admin)->put(
            route('admin.sections.update', [$page, $section]),
            [
                'data' => ['title' => 'Titre', 'items' => "A\nB"],
                'is_visible' => '1',
                'remove_item_image_0' => '1',
            ]
        )->assertRedirect(route('admin.pages.edit', $page));

        $section->refresh();
        $this->assertSame([1 => 'storage/sections/b.jpg'], $section->itemImages());
        Storage::disk('public')->assertMissing('sections/a.jpg');
    }

    public function test_deleting_a_section_removes_its_uploaded_files(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $section = $page->sections()->create([
            'type' => 'cards',
            'data' => [
                'image' => 'storage/sections/visuel.jpg',
                'images' => ['storage/sections/galerie.jpg'],
                'items' => [['title' => 'A', 'description' => '', 'image' => 'storage/sections/carte.jpg']],
            ],
            'sort_order' => 96,
        ]);

        foreach (['sections/visuel.jpg', 'sections/galerie.jpg', 'sections/carte.jpg'] as $path) {
            Storage::disk('public')->put($path, 'x');
        }

        $this->actingAs($admin)->delete(route('admin.sections.destroy', [$page, $section]));

        Storage::disk('public')->assertMissing('sections/visuel.jpg');
        Storage::disk('public')->assertMissing('sections/galerie.jpg');
        Storage::disk('public')->assertMissing('sections/carte.jpg');
        $this->assertModelMissing($section);
    }

    /* ------------------------------------------------------------------
     |  Rendu des formulaires d'administration
     * ------------------------------------------------------------------ */

    public function test_every_admin_media_form_renders(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'home')->first();
        $page->update([
            'hero_image' => 'storage/pages/hero.jpg',
            'hero_images' => ['storage/pages/hero-1.jpg', 'storage/pages/hero-2.jpg'],
        ]);
        foreach (['pages/hero.jpg', 'pages/hero-1.jpg', 'pages/hero-2.jpg'] as $path) {
            Storage::disk('public')->put($path, 'x');
        }

        // Formulaire de page : hero + galerie + vignettes de sections.
        $this->actingAs($admin)->get(route('admin.pages.edit', $page))
            ->assertOk()
            ->assertSee('name="hero_image_file"', false)
            ->assertSee('name="hero_gallery_files[]"', false)
            ->assertSee('name="remove_hero_gallery[]"', false)
            ->assertSee('name="remove_hero_image"', false);

        // Formulaire de section : image, galerie et photos par carte.
        foreach (array_keys(PageRendererService::SECTION_TYPES) as $type) {
            $section = $page->sections()->create([
                'type' => $type,
                'data' => [
                    'title' => 'Titre',
                    'items' => [['title' => 'Carte A', 'description' => 'Texte']],
                    'image' => 'storage/sections/apercu.jpg',
                    'images' => ['storage/sections/galerie.jpg'],
                ],
                'sort_order' => 90,
            ]);
            Storage::disk('public')->put('sections/apercu.jpg', 'x');
            Storage::disk('public')->put('sections/galerie.jpg', 'x');

            $this->actingAs($admin)->get(route('admin.sections.edit', [$page, $section]))
                ->assertOk()
                ->assertSee('name="data_file_image"', false)
                ->assertSee('name="data_file_images[]"', false)
                ->assertSee('name="remove_image"', false);

            if ($type === 'cards') {
                // La carte doit déjà avoir une photo pour que la case « supprimer » s'affiche.
                $section->update([
                    'data' => array_merge($section->data, [
                        'items' => [[
                            'title' => 'Carte A',
                            'description' => 'Texte',
                            'image' => 'storage/sections/carte.jpg',
                        ]],
                    ]),
                ]);
                Storage::disk('public')->put('sections/carte.jpg', 'x');

                $this->actingAs($admin)->get(route('admin.sections.edit', [$page, $section]))
                    ->assertOk()
                    ->assertSee('name="data_item_image_0"', false)
                    ->assertSee('name="remove_item_image_0"', false)
                    ->assertSee('storage/sections/carte.jpg');
            }
        }

        // Formulaire d'article : image principale + galerie.
        $post = Post::create([
            'title' => 'Avec images',
            'slug' => 'avec-images',
            'is_published' => true,
            'main_image' => 'storage/posts/principal.jpg',
            'gallery' => ['storage/posts/a.jpg', 'storage/posts/b.jpg'],
        ]);
        foreach (['posts/principal.jpg', 'posts/a.jpg', 'posts/b.jpg'] as $path) {
            Storage::disk('public')->put($path, 'x');
        }

        $this->actingAs($admin)->get(route('admin.posts.edit', $post))
            ->assertOk()
            ->assertSee('name="main_image_file"', false)
            ->assertSee('name="gallery_files[]"', false)
            ->assertSee('name="gallery_remove[]"', false)
            ->assertSee('name="remove_main_image"', false)
            ->assertSee('storage/posts/principal.jpg');
    }

    /* ------------------------------------------------------------------
     |  En-tête de page
     * ------------------------------------------------------------------ */

    public function test_admin_can_upload_and_remove_hero_gallery_images(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'about')->first();

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'is_published' => '1',
            'hero_gallery_files' => [
                UploadedFile::fake()->image('hero-1.jpg', 1200, 800),
                UploadedFile::fake()->image('hero-2.jpg', 1200, 800),
            ],
        ])->assertRedirect(route('admin.pages.edit', $page));

        $this->assertCount(2, $page->fresh()->heroGallery());

        $uploaded = $page->fresh()->heroGallery();
        $this->get('/a-propos')
            ->assertOk()
            ->assertSee($uploaded[0])
            ->assertSee($uploaded[1]);

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'is_published' => '1',
            'remove_hero_gallery' => ['1'],
        ])->assertRedirect(route('admin.pages.edit', $page));

        $gallery = $page->fresh()->heroGallery();
        $this->assertSame([$uploaded[0]], $gallery);
        Storage::disk('public')->assertExists(substr($uploaded[0], strlen('storage/')));
        Storage::disk('public')->assertMissing(substr($uploaded[1], strlen('storage/')));

        // La page reste affichable une fois la galerie vidée.
        $this->get('/a-propos')->assertOk()->assertDontSee($uploaded[1]);
    }

    public function test_hero_image_is_validated_and_removable(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $page = Page::where('page_key', 'about')->first();

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'is_published' => '1',
            'hero_image_file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('hero_image_file');

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'is_published' => '1',
            'hero_image_file' => UploadedFile::fake()->image('fond.jpg', 1600, 900),
        ])->assertRedirect(route('admin.pages.edit', $page));

        $page->refresh();
        $this->assertStringStartsWith('storage/pages/', $page->hero_image);
        $storedHero = $page->hero_image;

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'is_published' => '1',
            'remove_hero_image' => '1',
        ])->assertRedirect(route('admin.pages.edit', $page));

        $this->assertNull($page->fresh()->hero_image);
        Storage::disk('public')->assertMissing(substr($storedHero, strlen('storage/')));
    }

    public function test_external_image_url_is_not_prefixed_with_the_host(): void
    {
        $this->seed(DatabaseSeeder::class);

        $page = Page::where('page_key', 'about')->first();
        $page->update(['hero_image' => 'https://images.unsplash.com/photo-test.jpg']);

        $this->get('/a-propos')
            ->assertOk()
            ->assertSee('https://images.unsplash.com/photo-test.jpg')
            ->assertDontSee(url('/https://images.unsplash.com'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Realisation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RealisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_realisation_and_public_pages_display_it(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();

        $response = $this->actingAs($admin)->post(route('admin.realisations.store'), [
            'title' => 'Campagne de sensibilisation à Attikoumé',
            'description' => 'Sensibilisation des riverains à la salubrité publique.',
            'date' => '2026-05-12',
            'location' => 'Attikoumé',
            'is_published' => '1',
            'sort_order' => '0',
            'image_file' => UploadedFile::fake()->image('action.jpg', 800, 600),
        ]);

        $realisation = Realisation::where('title', 'Campagne de sensibilisation à Attikoumé')->first();
        $this->assertNotNull($realisation);
        $this->assertStringStartsWith('storage/realisations/', (string) $realisation->image);
        Storage::disk('public')->assertExists(substr((string) $realisation->image, strlen('storage/')));

        // Les vues d'administration se rendent sans erreur.
        $this->actingAs($admin)->get(route('admin.realisations.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.realisations.edit', $realisation))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();

        // Page publique : photo à gauche (realisation-row), texte à droite.
        $this->get('/realisations')
            ->assertOk()
            ->assertSee('realisation-row')
            ->assertSee('Campagne de sensibilisation à Attikoumé')
            ->assertSee('Attikoumé');

        // Accueil : les plus récentes apparaissent (limite 3) + bouton vers la page.
        $this->get('/')
            ->assertOk()
            ->assertSee('Campagne de sensibilisation à Attikoumé')
            ->assertSee('Voir toutes nos réalisations');
    }

    public function test_draft_realisation_is_hidden_from_public_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        Realisation::create([
            'title' => 'Action confidentielle',
            'is_published' => false,
        ]);

        $this->get('/realisations')->assertOk()->assertDontSee('Action confidentielle');
        $this->get('/')->assertOk()->assertDontSee('Action confidentielle');
    }

    public function test_image_validation_rejects_non_images(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@afriyol.org')->first();
        $countBefore = Realisation::count();

        $this->actingAs($admin)->post(route('admin.realisations.store'), [
            'title' => 'Test',
            'image_file' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('image_file');

        // Aucune réalisation créée : la validation rejette avant l'enregistrement.
        $this->assertSame($countBefore, Realisation::count());
    }
}

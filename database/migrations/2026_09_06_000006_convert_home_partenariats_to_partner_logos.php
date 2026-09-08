<?php

use App\Models\Page;
use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

/*
 * L'ancienne section « Partenariats » de la page d'accueil (cartes de texte)
 * est remplacée par la section « partners » : bandeau de logos défilant
 * horizontalement, géré depuis l'administration.
 *
 * Le contenu texte d'origine est conservé dans data.legacy_items afin de ne
 * rien perdre (il reste visible dans le commentaire du DatabaseSeeder).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $page = Page::query()->where('page_key', 'home')->first();

        if (! $page) {
            return;
        }

        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('type', 'cards')
            ->where('data->kicker', 'Partenariats')
            ->first();

        if (! $section) {
            return;
        }

        $data = $section->data ?? [];

        $section->update([
            'type' => 'partners',
            'data' => [
                'kicker' => $data['kicker'] ?? 'Partenariats',
                'title' => $data['title'] ?? 'Nos partenaires et collaborateurs',
                // Contenu texte d'origine, conservé pour référence.
                'legacy_items' => $data['items'] ?? [],
            ],
        ]);
    }

    public function down(): void
    {
        // Irréversible : les logos étaient vides à la conversion. Le contenu
        // texte d'origine reste disponible dans data.legacy_items et dans le
        // commentaire du DatabaseSeeder.
    }
};

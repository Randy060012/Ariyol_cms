<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $home = DB::table('pages')->where('page_key', 'home')->first(['id']);
        if (! $home) {
            return;
        }

        $sections = [
            'checklist' => [
                'kicker' => 'Qui sommes-nous ?',
                'title' => 'Des jeunes engagés aux côtés des communautés',
                'lead' => 'AFRIYOL agit avec les jeunes et les communautés pour faire avancer la paix, les droits humains et le développement durable.',
                'badge_title' => 'À vos côtés',
                'badge_text' => 'Des réponses construites avec les communautés',
                'button_label' => 'Découvrir notre mission',
                'button_url' => '/a-propos',
                'items' => [
                    ['title' => 'Éducation et leadership', 'description' => 'Des jeunes outillés pour construire leur avenir'],
                    ['title' => 'Paix et droits humains', 'description' => 'Le dialogue, la cohésion et l’inclusion au cœur des actions'],
                    ['title' => 'Environnement', 'description' => 'Des initiatives locales pour un cadre de vie durable'],
                ],
            ],
            'news' => [
                'kicker' => 'Actualités',
                'title' => 'Les nouvelles du terrain',
                'lead' => 'Les projets, les rencontres et les avancées des communautés.',
                'columns' => '3',
                'limit' => '3',
                'item_link_label' => 'Lire l’article',
                'button_label' => 'Toutes les actualités',
                'button_url' => '/blog',
            ],
        ];

        foreach ($sections as $type => $data) {
            if (DB::table('page_sections')->where('page_id', $home->id)->where('type', $type)->exists()) {
                continue;
            }

            $anchorType = $type === 'checklist' ? 'facts' : 'cta';
            $anchorPosition = DB::table('page_sections')->where('page_id', $home->id)->where('type', $anchorType)->value('sort_order');
            $position = $anchorPosition === null
                ? ((int) DB::table('page_sections')->where('page_id', $home->id)->max('sort_order')) + 1
                : (int) $anchorPosition + ($type === 'checklist' ? 1 : 0);
            DB::table('page_sections')->where('page_id', $home->id)->where('sort_order', '>=', $position)->increment('sort_order');

            $now = now();
            DB::table('page_sections')->insert([
                'page_id' => $home->id,
                'type' => $type,
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'sort_order' => $position,
                'is_visible' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // CMS sections may have been edited after migration; keep administrator content.
    }
};

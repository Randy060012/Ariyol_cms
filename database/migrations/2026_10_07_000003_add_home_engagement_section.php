<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $home = DB::table('pages')->where('page_key', 'home')->first(['id']);

        if (! $home || DB::table('page_sections')->where('page_id', $home->id)->where('type', 'engagement')->exists()) {
            return;
        }

        $before = DB::table('page_sections')->where('page_id', $home->id)->where('type', 'realisations')->value('sort_order');
        $position = $before === null
            ? ((int) DB::table('page_sections')->where('page_id', $home->id)->max('sort_order')) + 1
            : (int) $before;
        DB::table('page_sections')->where('page_id', $home->id)->where('sort_order', '>=', $position)->increment('sort_order');

        $now = now();
        DB::table('page_sections')->insert([
            'page_id' => $home->id,
            'type' => 'engagement',
            'data' => json_encode([
                'kicker' => 'Passer à l’action',
                'title' => 'Comment souhaitez-vous agir ?',
                'lead' => 'Chaque contribution aide les communautés à construire un avenir plus juste et durable.',
                'columns' => '3',
                'items' => [
                    ['title' => 'Soutenir nos actions', 'description' => 'Contribuez aux initiatives de terrain et à leur continuité.', 'url' => '/contact?subject=don', 'button_label' => 'Faire un don'],
                    ['title' => 'Devenir bénévole', 'description' => 'Mettez votre temps et vos compétences au service des communautés.', 'url' => '/contact?subject=benevole', 'button_label' => 'Nous rejoindre'],
                    ['title' => 'Créer un partenariat', 'description' => 'Construisons ensemble une action adaptée aux besoins locaux.', 'url' => '/contact?subject=partenariat', 'button_label' => 'Nous contacter'],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'sort_order' => $position,
            'is_visible' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // CMS content is retained during rollback so administrator edits are never discarded.
    }
};

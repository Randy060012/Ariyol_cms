<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Section;

class PageRendererService
{
    /**
     * Section types the admin can add, with labels.
     */
    public const SECTION_TYPES = [
        'facts' => 'Chiffres clés',
        'cards' => 'Cartes de contenu',
        'checklist' => 'Liste à puces',
        'quote' => 'Citation',
        'cta' => 'Appel à l\'action',
    ];

    /**
     * Resolve a page by slug or stable key and return it with its visible sections.
     */
    public static function resolve(string $slugOrKey): ?array
    {
        $page = Page::findPublishedBySlug($slugOrKey);

        if (! $page) {
            return null;
        }

        return [
            'page' => $page,
            'sections' => $page->visibleSections()->get(),
        ];
    }

    /**
     * Fields configuration for each section type (used by admin forms).
     */
    public static function sectionTypeFields(string $type): array
    {
        return match ($type) {
            'facts' => [
                'kicker' => ['label' => 'Sur-titre', 'type' => 'text'],
                'title' => ['label' => 'Titre', 'type' => 'text'],
                'image' => ['label' => 'Image (URL ou chemin)', 'type' => 'image'],
                'items' => ['label' => 'Éléments (un par ligne : Titre | Description)', 'type' => 'textarea', 'rows' => 5],
            ],
            'cards' => [
                'kicker' => ['label' => 'Sur-titre', 'type' => 'text'],
                'title' => ['label' => 'Titre', 'type' => 'text'],
                'image' => ['label' => 'Image (URL ou chemin)', 'type' => 'image'],
                'columns' => ['label' => 'Colonnes (2 ou 3)', 'type' => 'number'],
                'items' => ['label' => 'Cartes (une par ligne : Titre | Description)', 'type' => 'textarea', 'rows' => 6],
            ],
            'checklist' => [
                'kicker' => ['label' => 'Sur-titre', 'type' => 'text'],
                'title' => ['label' => 'Titre', 'type' => 'text'],
                'lead' => ['label' => 'Introduction', 'type' => 'text'],
                'image' => ['label' => 'Image (URL ou chemin)', 'type' => 'image'],
                'items' => ['label' => 'Points (un par ligne)', 'type' => 'textarea', 'rows' => 6],
            ],
            'quote' => [
                'quote' => ['label' => 'Citation', 'type' => 'textarea', 'rows' => 4],
                'author' => ['label' => 'Auteur', 'type' => 'text'],
                'role' => ['label' => 'Fonction', 'type' => 'text'],
                'image' => ['label' => 'Image (URL ou chemin)', 'type' => 'image'],
            ],
            'cta' => [
                'kicker' => ['label' => 'Sur-titre', 'type' => 'text'],
                'title' => ['label' => 'Titre', 'type' => 'text'],
                'text' => ['label' => 'Texte', 'type' => 'textarea', 'rows' => 3],
                'image' => ['label' => 'Image (URL ou chemin)', 'type' => 'image'],
                'button_label' => ['label' => 'Libellé du bouton', 'type' => 'text'],
                'button_url' => ['label' => 'Lien du bouton', 'type' => 'text'],
            ],
            default => [],
        };
    }

    /**
     * Parse the "Title | Description" textarea format into rows.
     */
    public static function parseItems(?string $raw): array
    {
        $items = [];

        foreach (preg_split('/\r\n|\r|\n/', (string) $raw) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$title, $description] = array_pad(explode('|', $line, 2), 2, '');

            $items[] = [
                'title' => trim($title),
                'description' => trim($description),
            ];
        }

        return $items;
    }

    /**
     * Render items payload from raw textarea for storage.
     */
    public static function buildData(string $type, array $input): array
    {
        $data = [];

        foreach (array_keys(static::sectionTypeFields($type)) as $field) {
            $data[$field] = $input[$field] ?? null;
        }

        if (in_array($type, ['facts', 'cards', 'checklist'], true) && isset($data['items'])) {
            $data['items'] = static::parseItems($data['items']);
        }

        return array_filter($data, fn ($value) => $value !== null && $value !== '');
    }
}

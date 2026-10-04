<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Section;

class PageRendererService
{
    /**
     * Storage folder for every image uploaded from a section.
     */
    public const SECTION_FOLDER = 'sections';

    /**
     * Section types the admin can add, with labels.
     */
    public const SECTION_TYPES = [
        'facts' => 'Chiffres clés',
        'cards' => 'Cartes de contenu',
        'checklist' => 'Liste à puces',
        'quote' => 'Citation',
        'partners' => 'Logos partenaires (défilement)',
        'realisations' => 'Réalisations (photo + texte)',
        'cta' => 'Appel à l\'action',
    ];

    /**
     * Section types whose individual items can carry their own photo.
     */
    public const ITEM_IMAGE_TYPES = ['cards'];

    /**
     * Field types understood by the admin form renderer.
     */
    public const FIELD_TEXT = 'text';

    public const FIELD_TEXTAREA = 'textarea';

    public const FIELD_NUMBER = 'number';

    public const FIELD_IMAGE = 'image';

    public const FIELD_GALLERY = 'gallery';

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
     *
     * Every section type exposes a single image and a gallery, so photos can
     * be added anywhere in the CMS.
     */
    public static function sectionTypeFields(string $type): array
    {
        $kicker = ['label' => 'Sur-titre', 'type' => self::FIELD_TEXT];
        $title = ['label' => 'Titre', 'type' => self::FIELD_TEXT];
        $image = ['label' => 'Image principale', 'type' => self::FIELD_IMAGE];
        $gallery = ['label' => 'Galerie d\'images de la section', 'type' => self::FIELD_GALLERY];

        // Les clés sont les noms des champs : le formulaire admin, la
        // validation et le stockage s'appuient toutes dessus.
        return match ($type) {
            'facts' => [
                'kicker' => $kicker,
                'title' => $title,
                'image' => $image,
                'images' => $gallery,
                'items' => ['label' => 'Éléments (un par ligne : Titre | Description)', 'type' => self::FIELD_TEXTAREA, 'rows' => 5],
            ],
            'cards' => [
                'kicker' => $kicker,
                'title' => $title,
                'image' => $image,
                'images' => $gallery,
                'columns' => ['label' => 'Colonnes (2 ou 3)', 'type' => self::FIELD_NUMBER],
                'items' => ['label' => 'Cartes (une par ligne : Titre | Description)', 'type' => self::FIELD_TEXTAREA, 'rows' => 6],
            ],
            'checklist' => [
                'kicker' => $kicker,
                'title' => $title,
                'lead' => ['label' => 'Introduction', 'type' => self::FIELD_TEXT],
                'image' => $image,
                'images' => $gallery,
                'items' => ['label' => 'Points (un par ligne)', 'type' => self::FIELD_TEXTAREA, 'rows' => 6],
            ],
            'quote' => [
                'quote' => ['label' => 'Citation', 'type' => self::FIELD_TEXTAREA, 'rows' => 4],
                'author' => ['label' => 'Auteur', 'type' => self::FIELD_TEXT],
                'role' => ['label' => 'Fonction', 'type' => self::FIELD_TEXT],
                'image' => ['label' => 'Portrait ou image de la citation', 'type' => self::FIELD_IMAGE],
                'images' => $gallery,
            ],
            'partners' => [
                'kicker' => $kicker,
                'title' => $title,
                'logos' => ['label' => 'Logos des partenaires', 'type' => self::FIELD_GALLERY],
                'image' => $image,
                'images' => $gallery,
            ],
            'realisations' => [
                'kicker' => ['label' => 'Sur-titre', 'type' => 'text'],
                'title' => ['label' => 'Titre', 'type' => 'text'],
                'limit' => ['label' => 'Nombre de réalisations à afficher (vide = toutes)', 'type' => 'number'],
                'button_label' => ['label' => 'Libellé du bouton (facultatif)', 'type' => 'text'],
                'button_url' => ['label' => 'Lien du bouton', 'type' => 'text'],
            ],
            'cta' => [
                'kicker' => $kicker,
                'title' => $title,
                'text' => ['label' => 'Texte', 'type' => self::FIELD_TEXTAREA, 'rows' => 3],
                'image' => $image,
                'images' => $gallery,
                'button_label' => ['label' => 'Libellé du bouton', 'type' => self::FIELD_TEXT],
                'button_url' => ['label' => 'Lien du bouton', 'type' => self::FIELD_TEXT],
            ],
            default => [],
        };
    }

    /**
     * Names of the single-image fields of a section type.
     *
     * @return array<int, string>
     */
    public static function imageFields(string $type): array
    {
        return static::fieldsOfType($type, self::FIELD_IMAGE);
    }

    /**
     * Names of the multi-image (gallery) fields of a section type.
     *
     * @return array<int, string>
     */
    public static function galleryFields(string $type): array
    {
        return static::fieldsOfType($type, self::FIELD_GALLERY);
    }

    /**
     * @return array<int, string>
     */
    protected static function fieldsOfType(string $type, string $fieldType): array
    {
        $fields = [];

        foreach (static::sectionTypeFields($type) as $name => $config) {
            if (($config['type'] ?? self::FIELD_TEXT) === $fieldType) {
                $fields[] = $name;
            }
        }

        return $fields;
    }

    /**
     * Can the individual items of this section type carry a photo?
     */
    public static function supportsItemImages(string $type): bool
    {
        return in_array($type, self::ITEM_IMAGE_TYPES, true);
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
     * Attach per-item photos (indexed by item position) to parsed items.
     *
     * The textarea only carries the text, so photos are re-attached by index
     * after parsing. Items without a photo are left untouched.
     *
     * @param  array<int, array<string, string|null>>  $items
     * @param  array<int, string|null>  $images
     * @return array<int, array<string, string|null>>
     */
    public static function attachItemImages(array $items, array $images): array
    {
        foreach ($items as $index => $item) {
            $image = $images[$index] ?? null;

            if (is_string($image) && $image !== '') {
                $items[$index]['image'] = $image;
            }
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

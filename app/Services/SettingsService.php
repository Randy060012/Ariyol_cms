<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class SettingsService
{
    /**
     * All settings grouped for the admin settings form.
     */
    public static function grouped(): array
    {
        if (! Schema::hasTable('site_settings')) {
            return [];
        }

        return Setting::query()
            ->orderBy('group')
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->values())
            ->all();
    }

    /**
     * Persist a whole settings form submission (key => value pairs).
     */
    public static function saveMany(array $values, array $types = [], array $groups = []): void
    {
        foreach ($values as $key => $value) {
            Setting::set(
                $key,
                $value,
                $types[$key] ?? 'text',
                $groups[$key] ?? 'general',
            );
        }
    }

    /**
     * Header brand data used by the public navbar.
     */
    public static function header(): array
    {
        return [
            'logo' => Setting::get('header_logo'),
            'brand_name' => Setting::get('brand_name', 'AFRIYOL'),
            'brand_tagline' => Setting::get('brand_tagline', 'African Young Leaders'),
            'cta_label' => Setting::get('header_cta_label', 'Rejoignez-nous'),
            'cta_url' => Setting::get('header_cta_url', '/contact'),
        ];
    }

    /**
     * Footer data used by the public footer partial.
     */
    public static function footer(): array
    {
        return [
            'logo' => Setting::get('footer_logo') ?? Setting::get('header_logo'),
            'brand_name' => Setting::get('brand_name', 'AFRIYOL'),
            'description' => Setting::get('footer_description', 'Organisation non gouvernementale dédiée aux Droits Humains, à la Paix, à l\'Environnement et au Leadership des Jeunes.'),
            'address' => Setting::get('contact_address', 'Tsévié, Daviémodji (Togo)'),
            'email' => Setting::get('contact_email', 'contact@afriyol.org'),
            'linkedin' => Setting::get('social_linkedin'),
            'facebook' => Setting::get('social_facebook'),
            'twitter' => Setting::get('social_twitter'),
            'copyright' => Setting::get('footer_copyright', 'Copyright © '.date('Y').' African Young Leaders (AFRIYOL). Tous droits réservés.'),
            'president' => Setting::get('footer_president', 'Président : M. SILIVI Koffi Victor'),
        ];
    }
}

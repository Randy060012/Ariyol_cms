<?php

namespace App\Http\View\Composers;

use App\Models\Page;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SiteComposer
{
    /**
     * Stable page keys mapped to their named routes.
     */
    public const CORE_KEYS = ['home', 'about', 'programmes', 'realisations', 'contact'];

    public function compose(View $view): void
    {
        $view->with('siteHeader', $this->header());
        $view->with('siteFooter', $this->footer());
        $view->with('navPages', $this->navPages());
    }

    private function header(): array
    {
        $data = SettingsService::header();

        if (empty($data['logo'])) {
            $data['logo'] = 'images/logo.svg';
        }

        return $data;
    }

    private function footer(): array
    {
        $data = SettingsService::footer();

        if (empty($data['logo'])) {
            $data['logo'] = 'images/logo.svg';
        }

        return $data;
    }

    /**
     * Menu built from published pages; falls back to the five core links.
     */
    private function navPages(): array
    {
        if (! Schema::hasTable('pages')) {
            return $this->fallbackMenu();
        }

        $pages = Page::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get(['title', 'slug', 'page_key']);

        if ($pages->isEmpty()) {
            return $this->fallbackMenu();
        }

        return $pages
            ->map(fn (Page $page) => [
                'label' => $page->title,
                'url' => $this->urlFor($page),
                'key' => $page->page_key,
            ])
            ->all();
    }

    private function urlFor(Page $page): string
    {
        return match ($page->page_key) {
            'home' => route('home'),
            'about' => route('about'),
            'programmes' => route('programmes'),
            'realisations' => route('realisations'),
            'contact' => route('contact'),
            default => '/'.$page->slug,
        };
    }

    private function fallbackMenu(): array
    {
        return [
            ['label' => 'Accueil', 'url' => route('home'), 'key' => 'home'],
            ['label' => 'À propos', 'url' => route('about'), 'key' => 'about'],
            ['label' => "Domaines d'action", 'url' => route('programmes'), 'key' => 'programmes'],
            ['label' => 'Réalisations', 'url' => route('realisations'), 'key' => 'realisations'],
            ['label' => 'Contact', 'url' => route('contact'), 'key' => 'contact'],
        ];
    }
}

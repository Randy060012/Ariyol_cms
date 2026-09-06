<?php

namespace App\Http\Controllers\Font;

use App\Http\Controllers\Controller;
use App\Services\PageRendererService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CmsPageController extends Controller
{
    /**
     * Render any CMS page by slug (core pages and custom pages alike).
     */
    public function show(Request $request, string $slug = 'home'): View
    {
        $resolved = PageRendererService::resolve($slug);

        abort_unless($resolved !== null, 404);

        /** @var \App\Models\Page $page */
        $page = $resolved['page'];
        $sections = $resolved['sections'];

        return view('pages.cms', [
            'page' => $page,
            'sections' => $sections,
            'metaTitle' => $page->meta_title ?: $page->hero_title ?: $page->title,
            'metaDescription' => $page->meta_description ?: $page->hero_subtitle,
        ]);
    }
}

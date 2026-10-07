<?php

namespace App\Http\Controllers\Font;

use App\Http\Controllers\Controller;
use App\Models\Realisation;
use App\Services\PageRendererService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RealisationController extends Controller
{
    public function index(Request $request): View
    {
        $resolved = PageRendererService::resolve('realisations');
        abort_unless($resolved !== null, 404);

        $query = Realisation::published()->ordered();
        $query->when($request->filled('theme'), fn ($builder) => $builder->where('category', $request->input('theme')));
        $query->when($request->filled('lieu'), fn ($builder) => $builder->where('location', $request->input('lieu')));

        $themes = Realisation::published()->whereNotNull('category')->where('category', '!=', '')
            ->distinct()->orderBy('category')->pluck('category');
        $locations = Realisation::published()->whereNotNull('location')->where('location', '!=', '')
            ->distinct()->orderBy('location')->pluck('location');

        $page = $resolved['page'];

        return view('pages.cms', [
            'page' => $page,
            'sections' => $resolved['sections'],
            'realisationResults' => $query->paginate(9)->withQueryString(),
            'realisationThemes' => $themes,
            'realisationLocations' => $locations,
            'activeTheme' => $request->input('theme'),
            'activeLocation' => $request->input('lieu'),
            'metaTitle' => $page->meta_title ?: $page->hero_title ?: $page->title,
            'metaDescription' => $page->meta_description ?: $page->hero_subtitle,
        ]);
    }

    public function show(Realisation $realisation): View
    {
        abort_unless($realisation->is_published, 404);

        return view('pages.realisation-show', [
            'realisation' => $realisation,
            'metaTitle' => $realisation->title.' | Réalisations AFRIYOL',
            'metaDescription' => $realisation->description ?: Str::limit(strip_tags((string) $realisation->content), 160),
        ]);
    }
}

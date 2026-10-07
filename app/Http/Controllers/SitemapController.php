<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Realisation;
use DateTimeInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    /**
     * Sitemap XML : pages CMS publiées, index du blog et articles publiés.
     */
    public function __invoke(): Response
    {
        $entries = [];

        if (Schema::hasTable('pages')) {
            foreach (Page::query()->where('is_published', true)->orderBy('sort_order')->get() as $page) {
                $entries[] = [
                    'loc' => $this->urlFor($page),
                    'lastmod' => $page->updated_at,
                    'priority' => $page->page_key === 'home' ? '1.0' : '0.8',
                ];
            }
        }

        $entries[] = [
            'loc' => route('blog.index'),
            'lastmod' => Post::tableExists()
                ? Post::published()->max('updated_at')
                : null,
            'priority' => '0.7',
        ];

        if (Post::tableExists()) {
            foreach (Post::published()->orderByDesc('created_at')->get(['slug', 'updated_at']) as $post) {
                $entries[] = [
                    'loc' => route('blog.show', $post->slug),
                    'lastmod' => $post->updated_at,
                    'priority' => '0.6',
                ];
            }
        }

        if (Realisation::tableExists()) {
            foreach (Realisation::published()->get(['slug', 'updated_at']) as $realisation) {
                if ($realisation->slug) {
                    $entries[] = [
                        'loc' => route('realisations.show', $realisation->slug),
                        'lastmod' => $realisation->updated_at,
                        'priority' => '0.6',
                    ];
                }
            }
        }

        $entries = array_map(function (array $entry) {
            $entry['lastmod'] = $this->toW3cDate($entry['lastmod']);

            return $entry;
        }, $entries);

        return response()
            ->view('sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * URL publique d'une page CMS (mêmes règles que le composer de navigation).
     */
    private function urlFor(Page $page): string
    {
        return match ($page->page_key) {
            'home' => route('home'),
            'about' => route('about'),
            'programmes' => route('programmes'),
            'realisations' => route('realisations'),
            'contact' => route('contact'),
            default => url('/'.$page->slug),
        };
    }

    /**
     * lastmod au format W3C Datetime (ex. 2026-09-06T22:14:38+00:00).
     */
    private function toW3cDate(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_W3C);
        }

        if (is_string($value) && $value !== '') {
            return Carbon::parse($value)->format(DATE_W3C);
        }

        return null;
    }
}

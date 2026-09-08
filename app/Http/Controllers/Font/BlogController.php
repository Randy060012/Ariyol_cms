<?php

namespace App\Http\Controllers\Font;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::tableExists()
            ? Post::published()
                ->ordered()
                ->when(
                    $request->filled('categorie'),
                    fn (Builder $q) => $q->where('category', $request->input('categorie'))
                )
                ->paginate(6)
                ->withQueryString()
            : Post::whereRaw('1 = 0')->paginate(6);

        $categories = Post::tableExists()
            ? Post::published()
                ->select('category')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
            : collect();

        return view('pages.blog', [
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $request->input('categorie'),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        return view('pages.blog-show', [
            'post' => $post,
            'metaTitle' => $post->title.' | Blog AFRIYOL',
            'metaDescription' => $post->excerpt ?: Str::limit(strip_tags((string) $post->content), 160),
        ]);
    }
}
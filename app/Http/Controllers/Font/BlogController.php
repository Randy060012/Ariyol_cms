<?php

namespace App\Http\Controllers\Font;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Post::tableExists()
            ? Post::published()->ordered()->get()
            : collect();

        return view('pages.blog', compact('posts'));
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
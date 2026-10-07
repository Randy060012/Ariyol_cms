<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Media;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $groups = Schema::hasTable('site_settings')
            ? SettingsService::grouped()
            : collect();

        return view('admin.settings', ['groups' => $groups]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'values' => ['required', 'array'],
            'values.*' => ['nullable', 'string'],
            'blog_hero_image_file' => ['nullable', ...Media::uploadRules()],
            'remove_blog_hero_image' => ['nullable', 'boolean'],
            'types' => ['nullable', 'array'],
            'groups' => ['nullable', 'array'],
        ]);

        $values = $validated['values'];

        // Handle image uploads (header_logo / footer_logo)
        foreach (['header_logo', 'footer_logo'] as $logoKey) {
            if ($request->hasFile("logo_files.{$logoKey}")) {
                $path = $request->file("logo_files.{$logoKey}")
                    ->store('logos', 'public');

                $values[$logoKey] = 'storage/'.$path;
            }
        }

        $currentBlogCover = Setting::get('blog_hero_image');
        if ($request->hasFile('blog_hero_image_file')) {
            Media::deleteStored($currentBlogCover);
            $values['blog_hero_image'] = Media::store($request->file('blog_hero_image_file'), 'pages');
        } elseif ($request->boolean('remove_blog_hero_image')) {
            Media::deleteStored($currentBlogCover);
            $values['blog_hero_image'] = '';
        }

        SettingsService::saveMany(
            $values,
            $validated['types'] ?? [],
            $validated['groups'] ?? []
        );

        return back()->with('success', 'Paramètres enregistrés.');
    }
}

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
            'logo_files.header_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'logo_files.footer_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'remove_header_logo' => ['nullable', 'boolean'],
            'remove_footer_logo' => ['nullable', 'boolean'],
            'blog_hero_image_file' => ['nullable', ...Media::uploadRules()],
            'remove_blog_hero_image' => ['nullable', 'boolean'],
            'types' => ['nullable', 'array'],
            'groups' => ['nullable', 'array'],
        ]);

        $values = $validated['values'];

        // Update or remove logos only when an administrator explicitly changes them.
        foreach (['header_logo', 'footer_logo'] as $logoKey) {
            $currentLogo = Setting::get($logoKey);

            if ($request->hasFile("logo_files.{$logoKey}")) {
                Media::deleteStored($currentLogo);
                $values[$logoKey] = Media::store($request->file("logo_files.{$logoKey}"), 'logos');
            } elseif ($request->boolean("remove_{$logoKey}")) {
                Media::deleteStored($currentLogo);
                $values[$logoKey] = $logoKey === 'header_logo' ? 'images/logo.svg' : '';
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

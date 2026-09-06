<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        SettingsService::saveMany(
            $values,
            $validated['types'] ?? [],
            $validated['groups'] ?? []
        );

        return back()->with('success', 'Paramètres enregistrés.');
    }
}

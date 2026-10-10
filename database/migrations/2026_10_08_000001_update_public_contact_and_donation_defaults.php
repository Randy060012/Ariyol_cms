<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SETTINGS = [
        'contact_email' => ['afriyol95@gmail.com', 'text', 'contact'],
        'contact_phone' => ['+22871462929', 'text', 'contact'],
        'header_cta_label' => ['Faire un don', 'text', 'header'],
        'header_cta_url' => ['/contact?subject=don', 'text', 'header'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        foreach (self::SETTINGS as $key => [$value, $type, $group]) {
            $attributes = ['value' => $value, 'type' => $type, 'group' => $group, 'updated_at' => now()];
            $setting = DB::table('site_settings')->where('key', $key);

            if ($setting->exists()) {
                $setting->update($attributes);
            } else {
                DB::table('site_settings')->insert($attributes + ['key' => $key, 'created_at' => now()]);
            }
        }

        $this->replaceContactEmailInPageContent('contact@afriyol.org', 'afriyol95@gmail.com');

        Cache::forget('site_settings');
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $previous = [
            'contact_email' => ['afriyol95@gmail.com', 'contact@afriyol.org'],
            'header_cta_label' => ['Faire un don', 'Rejoignez-nous'],
            'header_cta_url' => ['/contact?subject=don', '/contact'],
        ];

        foreach ($previous as $key => [$current, $old]) {
            DB::table('site_settings')->where('key', $key)->where('value', $current)->update([
                'value' => $old,
                'updated_at' => now(),
            ]);
        }

        DB::table('site_settings')->where('key', 'contact_phone')->where('value', '+22871462929')->delete();
        $this->replaceContactEmailInPageContent('afriyol95@gmail.com', 'contact@afriyol.org');
        Cache::forget('site_settings');
    }

    private function replaceContactEmailInPageContent(string $from, string $to): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $contactPageId = DB::table('pages')->where('page_key', 'contact')->value('id');
        if (! $contactPageId) {
            return;
        }

        foreach (DB::table('page_sections')->where('page_id', $contactPageId)->where('type', 'cta')->get(['id', 'data']) as $section) {
            $data = json_decode((string) $section->data, true);
            if (! is_array($data) || ! isset($data['text']) || ! str_contains($data['text'], $from)) {
                continue;
            }

            $data['text'] = str_replace($from, $to, $data['text']);
            DB::table('page_sections')->where('id', $section->id)->update(['data' => json_encode($data, JSON_UNESCAPED_UNICODE)]);
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('realisations', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->longText('content')->nullable();
            $table->json('gallery')->nullable();
        });

        DB::table('realisations')->orderBy('id')->get(['id', 'title'])->each(function ($item): void {
            $base = Str::slug($item->title) ?: 'realisation';
            $slug = $base;
            $suffix = 2;

            while (DB::table('realisations')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            DB::table('realisations')->where('id', $item->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('realisations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'content', 'gallery']);
        });
    }
};

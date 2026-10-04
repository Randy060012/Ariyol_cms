<?php

namespace Tests\Unit;

use App\Support\Media;
use Tests\TestCase;

class MediaTest extends TestCase
{
    public function test_absolute_urls_are_left_untouched(): void
    {
        $this->assertSame(
            'https://images.unsplash.com/photo.jpg',
            Media::url('https://images.unsplash.com/photo.jpg')
        );

        $this->assertSame('http://example.org/a.png', Media::url('http://example.org/a.png'));
        $this->assertSame('//cdn.example.org/a.png', Media::url('//cdn.example.org/a.png'));
    }

    public function test_relative_paths_are_resolved_against_the_application(): void
    {
        $this->assertSame(
            asset('storage/sections/abc.jpg'),
            Media::url('storage/sections/abc.jpg')
        );

        $this->assertSame(asset('images/logo.svg'), Media::url('images/logo.svg'));
    }

    public function test_empty_values_produce_no_url(): void
    {
        $this->assertNull(Media::url(null));
        $this->assertNull(Media::url(''));
        $this->assertNull(Media::url('   '));
        $this->assertSame('', Media::style(null));
    }

    public function test_only_stored_prefixes_are_considered_uploaded_files(): void
    {
        $this->assertTrue(Media::isStored('storage/sections/abc.jpg'));
        $this->assertFalse(Media::isStored('https://example.org/abc.jpg'));
        $this->assertFalse(Media::isStored('images/logo.svg'));
        $this->assertFalse(Media::isStored(null));
    }

    public function test_clean_drops_blank_entries_and_reindexes(): void
    {
        $this->assertSame(['a.jpg', 'b.jpg'], Media::clean([0 => 'a.jpg', 1 => '', 2 => null, 3 => 'b.jpg']));
        $this->assertSame([], Media::clean(null));
    }
}

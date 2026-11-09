<?php

declare(strict_types=1);

namespace Rinvex\Support\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class HelpersTest extends TestCase
{
    #[Test]
    public function it_loads_mimetypes(): void
    {
        $mimetypes = mimetypes();

        $this->assertIsArray($mimetypes);
        $this->assertNotEmpty($mimetypes);
    }

    #[Test]
    public function it_lists_timezones_keyed_by_identifier(): void
    {
        $timezones = timezones();

        $this->assertSame('America/New_York', $timezones['America/New_York']);
        $this->assertSame('UTC', $timezones['UTC']);
    }

    #[Test]
    public function it_lists_timeoffsets(): void
    {
        $this->assertSame('UTC -05:00', timeoffsets()['-0500']);
        $this->assertSame('UTC ±00:00', timeoffsets()['+0000']);
    }

    #[Test]
    public function it_searches_arrays_recursively(): void
    {
        $haystack = ['a' => 1, 'b' => ['c' => 2, 'd' => ['e' => 3]]];

        $this->assertSame('a', array_search_recursive(1, $haystack));
        $this->assertSame('b', array_search_recursive(3, $haystack));
        $this->assertFalse(array_search_recursive(4, $haystack));
    }

    #[Test]
    public function it_trims_arrays_recursively(): void
    {
        $this->assertSame(['a', ['b', 1]], array_trim_recursive([' a ', ["\tb\n", 1]]));
        $this->assertSame('x', array_trim_recursive(' x '));
    }

    #[Test]
    public function it_diffs_arrays_recursively(): void
    {
        $a = ['a' => 1, 'b' => ['c' => 2, 'd' => 3], 'e' => 4];
        $b = ['a' => 1, 'b' => ['c' => 2, 'd' => 5]];

        $this->assertSame(['b' => ['d' => 3], 'e' => 4], array_diff_assoc_recursive($a, $b));
        $this->assertSame(['b' => ['c' => 2, 'd' => 3], 'e' => 4], array_diff_assoc_recursive($a, $b, false));
    }

    #[Test]
    public function it_finds_contained_and_ending_strings(): void
    {
        $this->assertSame('bar', get_str_contains('foobarbaz', ['nope', 'bar']));
        $this->assertFalse(get_str_contains('foobarbaz', ['nope', '']));

        $this->assertSame('baz', get_str_endswith('foobarbaz', ['bar', 'baz']));
        $this->assertFalse(get_str_endswith('foobarbaz', 'bar'));
    }
}

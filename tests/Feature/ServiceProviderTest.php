<?php

declare(strict_types=1);

namespace Rinvex\Support\Tests\Feature;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Validator;
use Rinvex\Support\Tests\Stubs\Post;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_the_strip_tags_rule(): void
    {
        $this->assertTrue(Validator::make(['v' => 'plain text'], ['v' => 'strip_tags'])->passes());
        $this->assertTrue(Validator::make(['v' => '<b>bold</b>'], ['v' => 'strip_tags'])->fails());
    }

    #[Test]
    public function it_registers_the_timeoffset_rule(): void
    {
        $this->assertTrue(Validator::make(['v' => '-0500'], ['v' => 'timeoffset'])->passes());
        $this->assertTrue(Validator::make(['v' => '-0517'], ['v' => 'timeoffset'])->fails());
    }

    #[Test]
    public function it_registers_the_unique_with_rule(): void
    {
        Post::create(['title' => 'First', 'category' => 'news']);

        $rules = ['slug' => 'unique_with:posts,category'];

        $this->assertTrue(Validator::make(['slug' => 'first', 'category' => 'news'], $rules)->fails());
        $this->assertTrue(Validator::make(['slug' => 'first', 'category' => 'blog'], $rules)->passes());
        $this->assertTrue(Validator::make(['slug' => 'other', 'category' => 'news'], $rules)->passes());
    }

    #[Test]
    public function it_registers_the_collection_similar_macro(): void
    {
        $this->assertTrue(collect([1, 2, 3])->similar(collect([3, 2, 1])));
        $this->assertFalse(collect([1, 2, 3])->similar(collect([1, 2])));
        $this->assertTrue(Collection::hasMacro('similar'));
    }
}

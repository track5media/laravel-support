<?php

declare(strict_types=1);

namespace Rinvex\Support\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Rinvex\Support\Tests\Stubs\Post;

class ModelTraitsTest extends TestCase
{
    #[Test]
    public function it_generates_slugs_before_validation(): void
    {
        $post = Post::create(['title' => 'Hello World']);

        $this->assertTrue($post->exists);
        $this->assertSame('hello-world', $post->slug);
        $this->assertDatabaseHas('posts', ['slug' => 'hello-world']);
    }

    #[Test]
    public function it_generates_unique_slugs(): void
    {
        Post::create(['title' => 'Hello World']);
        $second = Post::create(['title' => 'Hello World']);

        $this->assertSame('hello-world-1', $second->slug);
    }

    #[Test]
    public function it_stores_and_reads_translations(): void
    {
        $post = Post::create(['title' => ['en' => 'Hello', 'fr' => 'Bonjour']]);
        $post = Post::find($post->id);

        $this->assertSame('Hello', $post->title);
        $this->assertSame('Bonjour', $post->getTranslation('title', 'fr'));
        $this->assertSame(['en' => 'Hello', 'fr' => 'Bonjour'], $post->getTranslations('title'));
        $this->assertSame(['title' => ['en' => 'Hello', 'fr' => 'Bonjour']], $post->getTranslations());
        $this->assertSame('Hello', $post->toArray()['title']);
    }

    #[Test]
    public function it_falls_back_to_the_first_translation(): void
    {
        $post = Post::create(['title' => ['fr' => 'Bonjour']]);

        $this->assertSame('Bonjour', Post::find($post->id)->title);
    }

    #[Test]
    public function it_drops_empty_translations(): void
    {
        $post = Post::create(['title' => ['en' => 'Hello', 'fr' => '']]);

        $this->assertSame(['en' => 'Hello'], Post::find($post->id)->getTranslations('title'));
    }

    #[Test]
    public function it_merges_translatable_attributes(): void
    {
        $post = new Post();
        $post->mergeTranslatable(['category']);

        $this->assertSame(['title', 'category'], $post->getTranslatableAttributes());
    }

    #[Test]
    public function it_validates_on_save(): void
    {
        $post = new Post(['title' => 'Valid']);
        $post->mergeRules(['category' => 'required']);

        $this->assertFalse($post->save());
        $this->assertTrue($post->getErrors()->has('category'));
        $this->assertDatabaseCount('posts', 0);

        $post->category = 'news';

        $this->assertTrue($post->save());
        $this->assertDatabaseCount('posts', 1);
    }

    #[Test]
    public function it_fires_validating_and_validated_events(): void
    {
        $fired = [];

        Post::validating(function () use (&$fired) {
            $fired[] = 'validating';
        });
        Post::validated(function () use (&$fired) {
            $fired[] = 'validated';
        });

        Post::create(['title' => 'Events']);

        $this->assertContains('validating', $fired);
        $this->assertContains('validated', $fired);
    }
}

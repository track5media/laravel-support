<?php

declare(strict_types=1);

namespace Rinvex\Support\Tests\Stubs;

use Spatie\Sluggable\SlugOptions;
use Rinvex\Support\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Support\Traits\HasTranslations;
use Rinvex\Support\Traits\ValidatingTrait;

class Post extends Model
{
    use HasSlug;
    use HasTranslations;
    use ValidatingTrait;

    protected $fillable = ['slug', 'title', 'category'];

    public $translatable = ['title'];

    protected $rules = [
        'slug' => 'required|alpha_dash|max:150',
        'title' => 'required',
    ];

    protected $validationMessages = [];

    protected $throwValidationExceptions = false;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->doNotGenerateSlugsOnUpdate()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}

<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like(): self
    {
        $this->likes()->create([
            'user_id' => auth()->id(),
        ]);

        return $this;
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }
}

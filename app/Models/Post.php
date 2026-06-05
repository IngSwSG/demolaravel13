<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $guarded = [];

    public function like()
    {
        return $this->likes()->create([
            'user_id' => auth()->id(),
        ]);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}

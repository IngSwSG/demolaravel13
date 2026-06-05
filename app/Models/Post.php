<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like(): Like
    {
        return $this->likes()->create([
            'user_id' => Auth::id(),
        ]);
    }
}
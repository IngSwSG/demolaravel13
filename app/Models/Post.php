<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

     public function like()
    {
        Like::create([
            'user_id' => Auth::id(),
            'likeable_id' => $this->id,
            'likeable_type' => self::class,
        ]);
    }
}

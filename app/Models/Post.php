<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function like()
    {
        $this->likes()->create([
            'user_id' => Auth::id(),
        ]);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
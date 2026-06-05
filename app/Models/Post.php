<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
    ];

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        if (! auth()->check()) {
            throw new Exception('Debe iniciar sesión para dar like.');
        }

        return $this->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);
    }
}

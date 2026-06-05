<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function add($users)
    {
        $newCount = $users instanceof User ? 1 : count($users);

        $this->guardAgainstTooManyMembers($newCount);

        if ($users instanceof User) {
            return $this->users()->save($users);
        }

        $this->users()->saveMany($users);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function guardAgainstTooManyMembers(int $adding = 1)
    {
        if ($this->users()->count() + $adding > $this->size) {
            throw new Exception();
        }
    }
}
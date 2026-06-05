<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    public function add($users)
    {
        $this->guardAgainstTooManyMembers($users);

        if ($users instanceof User) {
            return $this->users()->save($users);
        }

        $this->users()->saveMany($users);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function guardAgainstTooManyMembers($users)
    {
        $membersCount = $this->users()->count();
        $newMembersCount = $users instanceof User ? 1 : count($users);

        if ($membersCount + $newMembersCount > $this->size) {
            throw new Exception();
        }
    }
}

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
       
        $newUsersCount = ($users instanceof User) ? 1 : count($users);

        $this->guardAgainstTooManyMembers($newUsersCount);

        if ($users instanceof User) {
            return $this->users()->save($users);
        }

        $this->users()->saveMany($users);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function guardAgainstTooManyMembers($newUsersCount = 1)
    {
        
        if (($this->users()->count() + $newUsersCount) > $this->size) {
            throw new Exception("No se pueden agregar más usuarios. Se excede el tamaño máximo del equipo.");
        }
    }
}
<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
    ];

    public function add($users)
    {
        $this->guardAgainstTooManyMembers($users);

        if ($users instanceof User) {
            return $this->users()->save($users);
        }

        return $this->users()->saveMany($users);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function guardAgainstTooManyMembers($users)
    {
        $newUsersCount = $users instanceof User ? 1 : $users->count();

        if ($this->users()->count() + $newUsersCount > $this->size) {
            throw new Exception('El equipo no puede tener más miembros que su tamaño máximo.');
        }
    }
}

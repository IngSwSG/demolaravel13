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
        $this->guardAgainstTooManyMembers();
 
        if ($users instanceof User) {
            $this->users()->save($users);
            $this->unsetRelation('users'); // Refresca la relación cacheada
            return;
        }
 
        // Para colecciones, verifica que el total no supere el límite
        if ($this->users()->count() + count($users) > $this->size) {
            throw new Exception();
        }
 
        $this->users()->saveMany($users);
        $this->unsetRelation('users'); // Refresca la relación cacheada
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function guardAgainstTooManyMembers()
    {
        if ($this->users()->count() >= $this->size) {
            throw new Exception();
        }
    }
}

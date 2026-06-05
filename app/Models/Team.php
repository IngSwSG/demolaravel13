<?php


namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['size'];

    public function add($users)
    {
        $incomingCount = 1;
        if ($users instanceof \Illuminate\Support\Collection || is_array($users)) {
            $incomingCount = count($users);
        }

        $this->guardAgainstTooManyMembers($incomingCount);

        if ($users instanceof User) {
            return $this->users()->save($users);
        }

        return $this->users()->saveMany($users);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected function guardAgainstTooManyMembers($incomingCount = 1)
    {
        $currentCount = $this->users()->count();

        if (($currentCount + $incomingCount) > $this->size) {
            throw new Exception("El equipo superará el tamaño máximo permitido.");
        }
    }
}
<?php

use App\Models\Team;
use App\Models\User;

it('un equipo puede agrear usuarios', function () {
    $team = Team::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
 
    $team->add($user1);
    $team->add($user2);
 
    expect($team->fresh()->users)->toHaveCount(2);
});
 
it('un equipo puede tener un tamaño maximo', function () {
    $team = Team::factory()->create(['size' => 2]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
 
    $team->add($user1);
    $team->add($user2);
 
    expect($team->fresh()->users)->toHaveCount(2);
 
    $user3 = User::factory()->create();
 
    expect(fn() => $team->add($user3))->toThrow(Exception::class);
});
 
it('un equipo puede agregar multiples usuarios a la vez', function () {
    $team = Team::factory()->create(['size' => 3]);
    $users = User::factory(3)->create();
 
    $team->add($users);
 
    expect($team->fresh()->users)->toHaveCount(3);
});
 
// Prueba de regresión: verifica que el guard realmente impide agregar
// miembros más allá del límite (detecta el falso positivo original)
it('regresion: el guard lanza excepcion cuando se supera el tamaño del equipo', function () {
    $team = Team::factory()->create(['size' => 1]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
 
    $team->add($user1);
 
    // Esta prueba DEBE fallar si guardAgainstTooManyMembers está roto
    expect(fn() => $team->add($user2))->toThrow(Exception::class);
});
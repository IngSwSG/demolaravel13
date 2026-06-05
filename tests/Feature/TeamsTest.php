<?php

use App\Models\Team;
use App\Models\User;

it('un equipo puede agregar usuarios', function () {
    $team = Team::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $team->add($user1);
    $team->add($user2);

    expect($team->users)->count()->toBe(2);
});

it('un equipo puede tener un tamaño maximo', function () {
    $team = Team::factory()->create(['size' => 2]);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    
    $team->add($user1);
    $team->add($user2);

    expect($team->users)->count()->toBe(2);

    $this->expectException(Exception::class);
    $user3 = User::factory()->create();
    $team->add($user3);
});

it('un equipo puede agregar multiples usuarios a la vez', function () {
    $team = Team::factory()->create(['size' => 3]);
    $users = User::factory(3)->create();

    $team->add($users);

    expect($team->users)->count()->toBe(3);
});

// --- TEST DE REGRESIÓN (Solución al Falso Positivo) ---
it('no permite agregar multiples usuarios de golpe si superan el tamaño maximo', function () {

    $team = Team::factory()->create(['size' => 2]);
   
    $users = User::factory(3)->create();

    $this->expectException(Exception::class);
    
    $team->add($users);
});
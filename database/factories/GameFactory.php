<?php

use App\Board;
use Faker\Generator as Faker;

$factory->define(App\Game::class, function (Faker $faker) {
    return [
        'board' => Board::newBoard(),
        'player' => 'X',
        'ai' => '',
    ];
});

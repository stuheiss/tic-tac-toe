<?php

use App\Game;
use App\Board;
use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $game = factory(Game::class)->make();
        $game->save();

        // DB::table('games')->insert([
        //     'board' => Board::newBoard(),
        //     'player' => 'X',
        //     'ai' => '',
        // ]);
    }
}

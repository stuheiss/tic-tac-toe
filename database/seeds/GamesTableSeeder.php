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
        $game->board = json_encode($game->board);
        $game->save();

        // DB::table('games')->insert([
        //     'board' => json_encode(Board::newBoard()),
        //     'player' => 'X',
        //     'ai' => '',
        // ]);
    }
}

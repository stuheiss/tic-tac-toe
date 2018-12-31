<?php

namespace Tests\Feature;

use App\Game;
use App\Board;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameTest extends TestCase
{
    /** @test */
    public function ai_random_will_return_a_random_move()
    {
        $game = new Game;
        $board = Board::newBoard();
        $cell1 = $game->getAiRandom($board, null);
        $this->assertTrue(is_numeric($cell1['row']) && is_numeric($cell1['col']));
        for ($tries=0; ; ++$tries) { // might need a few tries to pass
            $cell2 = $game->getAiRandom($board, null);
            if ($cell1 != $cell2 || $tries >= 10) {
                $this->assertTrue(is_numeric($cell2['row']) && is_numeric($cell2['col']));
                $this->assertNotEquals($cell1, $cell2);
                break;
            }
        }
    }

    /** @test */
    public function ai_random_makes_excellent_first_move()
    {
        $game = new Game;
        $board = Board::newBoard();
        $cell = $game->getAiRandom($board, null);
        $this->assertNotFalse(array_search($cell, Board::corners()));
    }
}

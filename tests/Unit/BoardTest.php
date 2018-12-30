<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardTest extends TestCase
{
    /** @test */
    public function a_board_can_be_generated()
    {
        $board = \App\Board::newBoard();
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('123456789', $res);
    }

    /** @test */
    public function a_board_can_be_transposed()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::transpose($board);
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('147258369', $res);
    }

    /** @test */
    public function a_move_can_transform_to_coordinates()
    {
        list($row, $col) = \App\Board::moveToRowCol(6);
        $this->assertEquals([1,2], [$row,$col]);
    }

    /** @test */
    public function a_coordiante_pair_can_transform_to_a_move()
    {
        $move = \App\Board::moveFromRowCol(1, 2);
        $this->assertEquals(6, $move);
    }

    /** @test */
    public function a_player_can_move_to_an_empty_cell()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 0, 0, $board);
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('X23456789', $res);
    }

    /** @test */
    public function a_player_can_moveto_an_empty_cell()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::moveTo('X', 1, $board);
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('X23456789', $res);
    }

    /** @test */
    public function a_player_cannot_move_to_an_occupied_cell()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 0, 0, $board);
        $board2 = \App\Board::move('O', 0, 0, $board);

        $this->assertFalse($board2);
    }

    /** @test */
    public function a_list_of_empty_cells_can_be_generated()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 1, 1, $board);
        $cells = \App\Board::getEmptyCells($board);

        $res = collect($cells)->reduce(
            function ($acc, $cur) {
                return $acc . (string) $cur['row'] . (string) $cur['col'];
            }
        );
        $this->assertEquals('0001021012202122', $res);
    }

    /** @test */
    public function can_find_a_row_winner()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 0, 0, $board);
        $board = \App\Board::move('X', 0, 1, $board);
        $board = \App\Board::move('X', 0, 2, $board);

        $this->assertEquals('X', \App\Board::hasWinner($board));
    }

    /** @test */
    public function can_find_a_col_winner()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 0, 0, $board);
        $board = \App\Board::move('X', 1, 0, $board);
        $board = \App\Board::move('X', 2, 0, $board);

        $this->assertEquals('X', \App\Board::hasWinner($board));
    }

    /** @test */
    public function can_find_a_left_diagonal_winner()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 0, 0, $board);
        $board = \App\Board::move('X', 1, 1, $board);
        $board = \App\Board::move('X', 2, 2, $board);

        $this->assertEquals('X', \App\Board::hasWinner($board));
    }

    /** @test */
    public function can_find_a_right_diagonal_winner()
    {
        $board = \App\Board::newBoard();
        $board = \App\Board::move('X', 2, 0, $board);
        $board = \App\Board::move('X', 1, 1, $board);
        $board = \App\Board::move('X', 0, 2, $board);

        $this->assertEquals('X', \App\Board::hasWinner($board));
    }

    /** @test */
    public function can_determine_no_winner()
    {
        $board = \App\Board::newBoard();

        $this->assertNull(\App\Board::hasWinner($board));
    }

    /** @test */
    public function can_convert_move_to_row_col()
    {
        list($row, $col) = \App\Board::moveToRowCol(4);
        $this->assertEquals([$row, $col], [1, 0]);
    }

    /** @test */
    public function can_convert_row_col_to_move()
    {
        $move = \App\Board::moveFromRowCol(1, 0);
        $this->assertEquals($move, 4);
    }
}

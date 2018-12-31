<?php

namespace Tests\Unit;

use App\Board;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardTest extends TestCase
{
    /** @test */
    public function a_board_can_be_generated()
    {
        $board = Board::newBoard();
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('123456789', $res);
    }

    /** @test */
    public function a_board_can_be_transposed()
    {
        $board = Board::newBoard();
        $board = Board::transpose($board);
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('147258369', $res);
    }

    /** @test */
    public function a_move_can_transform_to_coordinates()
    {
        list($row, $col) = Board::numberToRowCol(6);
        $this->assertEquals([1,2], [$row,$col]);
    }

    /** @test */
    public function a_coordiante_pair_can_transform_to_a_move()
    {
        $move = Board::numberFromRowCol(1, 2);
        $this->assertEquals(6, $move);
    }

    /** @test */
    public function a_board_has_four_unique_corners()
    {
        $corners = Board::corners();
        $this->assertEquals(4, count(array_unique($corners, SORT_REGULAR)));
    }

    /** @test */
    public function a_player_can_move_to_an_empty_cell()
    {
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 1, $board);
        $res = collect($board)->collapse()->reduce(function ($acc, $cur) {
            return $acc . $cur;
        });
        $this->assertEquals('X23456789', $res);
    }

    /** @test */
    public function a_player_cannot_move_to_an_occupied_cell()
    {
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 1, $board);
        $board2 = Board::playerMoveTo('O', 1, $board);

        $this->assertFalse($board2);
    }

    /** @test */
    public function a_list_of_empty_cells_can_be_generated()
    {
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 5, $board);
        $cells = Board::getEmptyCells($board);

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
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 1, $board);
        $board = Board::playerMoveTo('X', 2, $board);
        $board = Board::playerMoveTo('X', 3, $board);

        $this->assertEquals('X', Board::hasWinner($board));
    }

    /** @test */
    public function can_find_a_col_winner()
    {
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 1, $board);
        $board = Board::playerMoveTo('X', 4, $board);
        $board = Board::playerMoveTo('X', 7, $board);

        $this->assertEquals('X', Board::hasWinner($board));
    }

    /** @test */
    public function can_find_a_left_diagonal_winner()
    {
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 1, $board);
        $board = Board::playerMoveTo('X', 5, $board);
        $board = Board::playerMoveTo('X', 9, $board);

        $this->assertEquals('X', Board::hasWinner($board));
    }

    /** @test */
    public function can_find_a_right_diagonal_winner()
    {
        $board = Board::newBoard();
        $board = Board::playerMoveTo('X', 3, $board);
        $board = Board::playerMoveTo('X', 5, $board);
        $board = Board::playerMoveTo('X', 7, $board);

        $this->assertEquals('X', Board::hasWinner($board));
    }

    /** @test */
    public function can_determine_no_winner()
    {
        $board = Board::newBoard();

        $this->assertNull(Board::hasWinner($board));
    }

    /** @test */
    public function can_convert_move_to_row_col()
    {
        list($row, $col) = Board::numberToRowCol(4);
        $this->assertEquals([$row, $col], [1, 0]);
    }

    /** @test */
    public function can_convert_row_col_to_move()
    {
        $move = Board::numberFromRowCol(1, 0);
        $this->assertEquals($move, 4);
    }
}

<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Game extends Model
{
    public function getBoard()
    {
        return json_decode($this->board);
        // return Session::get('board');
    }

    public function getPlayer()
    {
        return $this->player;
        // return Session::get('player');
    }

    public function getAi()
    {
        return $this->ai;
        // return Session::get('ai');
    }

    public function setBoard($board)
    {
        // Session::put('board', $board);
        $this->board = json_encode($board);
        $this->save();
    }

    public function setPlayer($player)
    {
        // Session::put('player', $player);
        $this->player = $player;
        $this->save();
    }

    public function setAi($ai)
    {
        // Session::put('ai', $ai);
        $this->ai = $ai;
        $this->save();
    }

    public function newGame($ai = null)
    {
        $this->setBoard(Board::newBoard());
        $this->setPlayer('X');
        $this->setAi($ai);
    }

    public function makeMove($move, $board, $player)
    {
        return Board::playerMoveTo($player, $move, $board);
    }

    public function getAiRandom($board, $player)
    {
        $cells = Board::getEmptyCells($board);
        if (count($cells) === Board::ROWS * BOARD::COLS) {
            // first move
            return $this->getExcellentFirstMove();
        }
        $emptyCells = Board::getEmptyCells($board);
        $cell = array_random($emptyCells);
        return $cell;
    }

    public function getAiWinning($board, $player)
    {
        $cells = Board::getEmptyCells($board);
        if (count($cells) === Board::ROWS * BOARD::COLS) {
            // first move
            return $this->getExcellentFirstMove();
        }
        foreach ($cells as $cell) {
            $newBoard = Board::playerMoveTo($player, Board::numberFromRowCol($cell['row'], $cell['col']), $board);
            $winner = Board::hasWinner($newBoard);
            if ($winner) {
                return $cell;
            }
        }
        return $this->getAiRandom($board, $player);
    }

    public function getAiMinimax($board, $player)
    {
        $data = [];

        $cells = Board::getEmptyCells($board);
        if (count($cells) === Board::ROWS * BOARD::COLS) {
            // first move
            return $this->getExcellentFirstMove();
        }
        foreach ($cells as $cell) {
            $newBoard = Board::playerMoveTo($player, Board::numberFromRowCol($cell['row'], $cell['col']), $board);

            $opponent = $this->opponent($player);

            $score = $this->minimaxScore($newBoard, $opponent, $player);
            if (!array_key_exists($score, $data)) $data[$score] = [];
            $data[$score][] = $cell;
        }

        // return a random choice of all the best moves
        $max = max(array_keys($data));
        $arrayOfCellsWithMaxScore = array_filter($data, function ($k) use ($max) { return $k==$max; }, ARRAY_FILTER_USE_KEY);
        $cells = array_pop($arrayOfCellsWithMaxScore);
        $cell = array_random($cells);

        return $cell;
    }

    public function opponent($player)
    {
        return $player === 'X' ? 'O' : 'X';
    }

    public function getExcellentFirstMove()
    {
        // corner moves are best first moves
        $moves = [
            ['row' => 0, 'col' => 0],
            ['row' => 0, 'col' => 2],
            ['row' => 2, 'col' => 0],
            ['row' => 2, 'col' => 2],
        ];
        return array_random($moves);
    }

    /**
     * minimaxScore
     * 
     * If X has won, immediately return +10
     * If O has won, immediately return -10
     * If the state is a draw, immediately return 0
     * For each possible move for the current player, get the state that would result if that move was made
     * For each of these states, use the minimax_score function to calculate its score. Store all of these scores in an array
     * If X is the current player, return the max of the scores resulting from each possible move (maximin-ing)
     * If O is the current player, return the min of the scores (minimax-ing)
     *
     * @param array  $board            the ttt board
     * @param string $player           the current player
     * @param string $playerToOptimize the AI player
     * 
     * @return integer
     */
    public function minimaxScore($board, $player, $playerToOptimize, $level=0)
    {
        if ($winner = Board::hasWinner($board)) {
            return $winner === $playerToOptimize ? 10 : -10;
        } else if (!($cells = Board::getEmptyCells($board))) {
            return 0;
        }

        $scores = [];
        foreach ($cells as $cell) {
            $newBoard = Board::playerMoveTo($player, Board::numberFromRowCol($cell['row'], $cell['col']), $board);

            $opponent = $this->opponent($player);
            $score = $this->minimaxScore($newBoard, $opponent, $playerToOptimize, $level+1);
            $scores[] = $score;
        }
        return ($player === $playerToOptimize) ? max($scores) : min($scores);
    }
}
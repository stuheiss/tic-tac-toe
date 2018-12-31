<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    const ROWS = 3;
    const COLS = 3;

    public static function newBoard()
    {
        $board = [];
        for ($row = 0; $row < self::ROWS; ++$row) {
            $board[$row] = [];
            for ($col = 0; $col < self::COLS; ++$col) {
                $board[$row][$col] = (string) $row * self::ROWS + $col + 1; // make 1 based
            }
        }
        return $board;
    }

    public static function numberToRowCol($number)
    {
        $number--; // make 0 based
        $row = intdiv($number, self::ROWS);
        $col = $number % self::ROWS;
        return [$row, $col];
    }

    public static function numberFromRowCol($row, $col)
    {
        return $row * self::ROWS + $col + 1;
    }

    public static function playerMoveTo($player, $move, $board)
    {
        list($row, $col) = self::numberToRowCol($move);
        if (!is_numeric($board[$row][$col])) return false;
        $board[$row][$col] = $player;
        return $board;
    }

    public static function getEmptyCells($board)
    {
        $moves=[];
        if (!$board) {
            return $moves;
        }
        for ($row = 0; $row < self::ROWS; ++$row) {
            for ($col = 0; $col < self::COLS; ++$col) {
                if (is_numeric($board[$row][$col])) {
                    $moves[] = ['row' => $row, 'col' => $col];
                }
            }
        }
        return $moves;
    }

    public static function transpose($board)
    {
        $newBoard = [];
        for ($row = 0; $row < self::ROWS; ++$row) {
            $newBoard[$row] = [];
            for ($col = 0; $col < self::COLS; ++$col) {
                $newBoard[$row][$col] = $board[$col][$row];
            }
        }
        return $newBoard;
    }

    public static function diagonals($board)
    {
        // assume ROWS === COLS
        $res = [];
        $left = [];
        $right = [];
        for ($i = 0, $j = self::ROWS - 1; $i < self::ROWS; ++$i, --$j) {
            $left[] = $board[$i][$i];
            $right[] = $board[$j][$i];
        }
        return [$left, $right];
    }

    public static function hasWinner($board)
    {
        if (!$board) {
            return null;
        }
        // check all possible winning lists of 3 cells
        $rows = array_merge($board, self::transpose($board), self::diagonals($board));

        for ($i = 0; $i < count($rows); ++$i) {
            $res = collect($rows[$i])->reduce(
                function ($acc, $cur) {
                    return $acc . $cur;
                }
            );
            if ($res === 'XXX' || $res === 'OOO') {
                return $res[0];
            }
        }
        return null;
    }

    public static function corners()
    {
        $corners = [];
        foreach ([0, self::ROWS - 1] as $row) {
            foreach ([0, self::COLS -1] as $col) {
                $corners[] = ['row' => $row, 'col' => $col];
            }
        }
        return $corners;
    }
}

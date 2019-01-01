<?php

namespace App\Http\Controllers;

use App\Game;
use App\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    protected $game;

    const AISTRATEGY = [
        'easy' => 'getAiRandom',
        'medium' => 'getAiWinning',
        'hard' => 'getAiMinimax'
    ];

    public function __construct()
    {
        $this->game = (new Game)->find(1);
    }

    public function index()
    {
        $board = $this->game->board;
        $player = $this->game->player;
        $ai = $this->game->ai;
        $winner = Board::hasWinner($board);
        $emptyCells = Board::getEmptyCells($board);

        if ($ai && $player === 'X' && !$winner && $emptyCells) {
            $strategy = self::AISTRATEGY[$ai];
            $cell = $this->game->$strategy($board, $player);
            $move = Board::numberFromRowCol($cell['row'], $cell['col']);
            $newBoard = $this->game->makeMove($move, $board, $player);
            $this->game->board = $newBoard;
            $this->game->player = $this->game->opponent($player);
            $this->game->save();
            // flash last and new status
            $previousStatus = Session::get('status');
            return redirect('/')->with('status', "$previousStatus, AI plays $move");
        }

        return view(
            'index', [
                'board' => $board,
                'player' => $player,
                'winner' => $winner,
                'playsleft' => count($emptyCells),
                'ai' => $ai,
            ]
        );
    }

    public function play(Request $request)
    {
        $difficulty = $request->difficulty;
        $this->game->newGame($difficulty);
        return redirect('/')->with('status', 'Good luck champ');
    }

    public function move(Request $request)
    {
        $board = $this->game->board;
        $player = $this->game->player;
        $winner = Board::hasWinner($board);
        $emptyCells = Board::getEmptyCells($board);
        if ($winner || !$emptyCells) {
            return redirect('/')->with('status', 'Game Over Dude!!!');
        }
        $move = $request->validate(['move' => 'required|integer|between:1,9'])['move'];
        if ($newBoard = $this->game->makeMove($move, $board, $player)) {
            $this->game->board = $newBoard;
            $this->game->player = $this->game->opponent($player);
            $this->game->save();
            return redirect('/')->with('status', "{$player} plays $move");
        } else {
            return redirect('/')->with('status', "Illegal move, try again");
        }
    }
}

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
        // $this->game = new Game;
        $this->game = (new Game)->find(1);
    }

    public function index()
    {
        $board = $this->game->getBoard();
        $player = $this->game->getPlayer();
        $ai = $this->game->getAi();
        $winner = Board::hasWinner($board);
        $emptyCells = Board::getEmptyCells($board);

        if ($ai && $player === 'X' && !$winner && $emptyCells) {
            $strategy = self::AISTRATEGY[$ai];
            $cell = $this->game->$strategy($board, $player);
            $move = Board::numberFromRowCol($cell['row'], $cell['col']);
            if ($newBoard = $this->game->makeMove($move, $board, $player)) {
                $this->game->setBoard($newBoard);
                $this->game->setPlayer($player == 'X' ? 'O' : 'X');
                $old = Session::get('status');
                return redirect('/')->with('status', "$old, AI plays $move");
            } else {
                $old = Session::get('status');
                return redirect('/')->with('status', "$old, Illegal move, try again");
            }
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
        if ($difficulty === null) $difficulty = '';
        $this->game->newGame($difficulty);
        return redirect('/')->with('status', 'Good luck champ');
    }

    public function move(Request $request)
    {
        $board = $this->game->getBoard();
        $player = $this->game->getPlayer();
        $winner = Board::hasWinner($board);
        $emptyCells = Board::getEmptyCells($board);
        if ($winner || !$emptyCells) {
            return redirect('/')->with('status', 'Game over dude!!!');
        }
        $move = $request->validate(['move' => 'required|integer|between:1,9'])['move'];
        if ($newBoard = $this->game->makeMove($move, $board, $player)) {
            $this->game->setBoard($newBoard);
            $this->game->setPlayer($player == 'X' ? 'O' : 'X');
            return redirect('/')->with('status', "{$player} plays {$move}");
        } else {
            return redirect('/')->with('status', 'Illegal move, try again');
        }
    }
}

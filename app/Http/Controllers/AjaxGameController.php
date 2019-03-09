<?php

namespace App\Http\Controllers;

use App\Game;
use App\Puzzle;
use Illuminate\Http\Request;

class AjaxGameController extends Controller
{
    public function show(int $id) {
        $game = Game::findOrFail($id);
        return response()->json($game)->cookie("game_id", $id, 1440);
    }

    public function store() {
        $puzzle = Puzzle::inRandomOrder()->first();
        $game = Game::create([
            "moves" => $puzzle->start,
            "puzzle_id" => $puzzle->id,
        ]);
        return $this->show($game->id);
    }

    public function update(Request $request, int $id) {
        $game = Game::findOrFail($id);
        if (!$game->is_complete) {
            $game->moves = $request->input("moves");
            $game->save();
        }
        return $this->show($id);
    }

    public function destroy(int $id) {
        $game = Game::findOrFail($id);
        $game->is_complete = 1;
        $game->save();
        return $this->show($id);
    }
}


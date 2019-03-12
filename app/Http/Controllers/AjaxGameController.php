<?php

namespace App\Http\Controllers;

use App\Game;
use App\Puzzle;
use Cookie;
use Illuminate\Http\Request;

class AjaxGameController extends Controller
{
    public function show(int $id) {
        $game = Game::findOrFail($id);
        if ($game->player_id == Cookie::get("player_id"))
            return response()->json($game)->cookie("game_id", $id, 1440);
        return response()->json([]);
    }

    public function store(Request $request) {
        $difficulty = $request->input("difficulty") ?? 1;
        $puzzle = Puzzle::where("difficulty", $difficulty)->inRandomOrder()->first();
        $game = Game::create([
            "moves" => $puzzle->start,
            "puzzle_id" => $puzzle->id,
            "player_id" => Cookie::get("player_id") ?? md5("Sud0ku"),
        ]);
        return $this->show($game->id);
    }

    public function update(Request $request, int $id) {
        $game = Game::findOrFail($id);
        if ($game->player_id == Cookie::get("player_id") && !$game->is_complete) {
            $game->moves = $request->input("moves");
            $game->save();
        }
        return $this->show($id);
    }

    public function destroy(int $id) {
        $game = Game::findOrFail($id);
        if ($game->player_id == Cookie::get("player_id")) {
            $game->is_complete = 1;
            $game->save();
        }
        return $this->show($id);
    }
}


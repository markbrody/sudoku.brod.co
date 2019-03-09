<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $appends = ["starting_cells", "incorrect_cells"];

    protected $fillable = ["moves", "puzzle_id"];

    protected $hidden = ["puzzle_id", "created_at", "updated_at", "puzzle"];

    public function puzzle() {
        return $this->belongsTo("App\Puzzle");
    }

    public function getMovesAttribute($value) {
        return $this->to_array($value);
    }

    public function getStartingCellsAttribute() {
        $starting_cells = [];
        $start = $this->to_array($this->puzzle->start);
        foreach ($start as $cell => $value)
            if ($value > 0)
                $starting_cells[] = $cell;
        return $starting_cells;
    }

    public function getIncorrectCellsAttribute() {
        $solution = $this->to_array($this->puzzle->solution);
        $incorrect_cells = [];
        foreach ($this->moves as $cell => $value)
            if ($value > 0 && $value !== $solution[$cell])
                $incorrect_cells[] = $cell;
        return $incorrect_cells;
        
    }

    private function to_array($str) {
        return array_map("intval", str_split($str));
    }
}

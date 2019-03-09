<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    protected $fillable = ["start", "solution", "difficulty"];

    public function games() {
        return $this->hasMany("App\Game");
    }
}

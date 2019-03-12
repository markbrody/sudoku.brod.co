<?php

namespace App\Http\Controllers;

use Cookie;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request) {
        $player_id = Cookie::get("player_id") ?? md5(time());
        Cookie::queue("player_id", $player_id, 43200);
        return view("index");
    }
}

<?php

namespace App;

use App\Puzzle;

Class Sudoku
{
    const EMPTY_GRID = [
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
        0, 0, 0, 0, 0, 0, 0, 0, 0,
    ];

    public $grid;

    public $moves;

    public function __construct(array $grid=self::EMPTY_GRID) {
        $this->grid = $grid;
        $this->moves = 0;
    }

    public function solve($print=false) {
        $saved = [];    
        $saved_sud = [];
        while (!$this->is_solved_sudoku()) {
            $this->moves++; 
            $next_move = $this->scan_sudoku_for_unique();
            if ($next_move == false) {
                $next_move = array_pop($saved);
                $this->grid = array_pop($saved_sud);
            }
            $what_to_try = $this->next_random($next_move);    
            $attempt = $this->determine_random_possible_value($next_move, $what_to_try);
            if (count($next_move[$what_to_try]) > 1) {                    
                $next_move[$what_to_try] = $this->remove_attempt($next_move[$what_to_try], $attempt);
                array_push($saved, $next_move);
                array_push($saved_sud, $this->grid);
            }
            $this->grid[$what_to_try] = $attempt;    
        }
        if ($print)
            echo $this->print_sudoku();
    }

    private function which_row($cell) {
        return floor($cell / 9);
    }

    private function which_column($cell) {
        return $cell % 9;
    }

    private function which_block($cell) {
        return floor($this->which_row($cell) / 3) * 3 + floor($this->which_column($cell) / 3);
    }

    private function is_valid_row($number, $row) {
        $possible = true;
        for ($x=0; $x<=8; $x++)
            if ($this->grid[$row * 9 + $x] == $number)
                $possible = false;
        return $possible;
    }

    private function is_valid_column($number, $column) {
        $possible = true;
        for ($x=0; $x<=8; $x++)
            if ($this->grid[$column + 9 * $x] == $number)
                $possible = false;
        return $possible;
    }

    private function is_valid_block($number, $block) {
        $possible = true;
        for ($x=0;$x<=8;$x++)
            if ($this->grid[floor($block / 3) * 27 + $x %3 + 9 * floor($x / 3) + 3 * ($block % 3)] == $number)
                $possible = false;
        return $possible;
    }

    private function is_valid_number($cell, $number) {
        return $this->is_valid_row($number, $this->which_row($cell))
            && $this->is_valid_column($number, $this->which_column($cell))
            && $this->is_valid_block($number, $this->which_block($cell));
    }    
    
    // private function print_sudoku() {
    //     $html = '<table bgcolor="#000" cellspacing="1" style="border: 1px solid #000;">';
    //     for ($row=0; $row<3; $row++) {
    //         $html .= '<tr>';
    //         for ($col=0; $col<3; $col++) {
    //             $html .= '<td><table cellspacing="1">';
    //             for ($i=0; $i<3; $i++) {
    //                 $html .= '<tr>';
    //                 for ($j=0; $j<3; $j++)
    //                     $html .= '<td bgcolor="#fff" width="40" height="40" align="center">'
    //                             // . ($this->grid[($row * 3 + $i) * 9 + $col * 3 + $j] ?: "")
    //                             . ($this->grid[($row * 3 + $i) * 9 + $col * 3 + $j] ?: '<input type="text" style="width:40px;height:40px;line-height:40px;padding:0;margin:0;text-align:center;" maxlength="1">')
    //                             . '</td>';
    //                 $html .= '</tr>';
    //             }
    //             $html .= '</table></td>';
    //         }
    //         $html .= '</tr>';
    //     }
    //     $html .= '</table>';
    // 
    //     return $html;
    // }

    private function is_correct_row($row) {
        for ($x=0; $x<=8; $x++)
            $row_temp[$x] = $this->grid[$row * 9 + $x];
        return count(array_diff([1,2,3,4,5,6,7,8,9], $row_temp)) == 0;
    }

    private function is_correct_column($column) {
        for ($x=0; $x<=8; $x++)
            $col_temp[$x] = $this->grid[$column + $x * 9];
        return count(array_diff([1,2,3,4,5,6,7,8,9], $col_temp)) == 0;
    }

    private function is_correct_block($block) {
        for ($x=0;$x<=8;$x++)
            $block_temp[$x] = $this->grid[floor($block / 3) * 27 + $x % 3 + 9 *floor($x / 3) + 3 * ($block % 3)];
        return count(array_diff([1,2,3,4,5,6,7,8,9], $block_temp)) == 0;
    }

    private function is_solved_sudoku() {
        for ($x=0;$x<=8;$x++)
            if (!$this->is_correct_block($x) || !$this->is_correct_row($x) || !$this->is_correct_column($x))
                return false;
        return true;
    }

    private function determine_possible_values($cell) {
        $possible = [];
        for ($x=1; $x<=9; $x++)
            if ($this->is_valid_number($cell, $x))
                array_unshift($possible, $x);
        return $possible;
    }

    private function determine_random_possible_value($possible, $cell) {
        return $possible[$cell][rand(0,count($possible[$cell])-1)];
    }

    private function scan_sudoku_for_unique() {
        for ($x=0; $x<=80; $x++) {
            if ($this->grid[$x] == 0) {
                $possible[$x] = $this->determine_possible_values($x);
                if (count($possible[$x])==0) {
                    return false;
                }
            }
        }
        return($possible);
    }

    private function remove_attempt($attempt_array, $number) {
        $new_array = [];
        for ($x=0;$x<count($attempt_array);$x++) {
            if ($attempt_array[$x] != $number) {
                array_unshift($new_array,$attempt_array[$x]);
            }
        }
        return $new_array;
    }

    private function next_random($possible) {
        $max = 9;
        for ($x=0;$x<=80;$x++) {
            if (((@count($possible[$x])) <= $max) and ((@count($possible[$x])) > 0)) {
                $max = count($possible[$x]);
                $min_choices = $x;
            }
        }
        return $min_choices;
    }

    public function generate() {
        $this->solve();
        $i = 0;
        while ($i < 10) // remove 20
            if ($this->symmetric_removal())
                $i++;
        $i = 0;
        while ($i < rand(29, 39)) { // 23 to 33 squares will remain
            if ($this->individual_removal())
                $i++;
        }
        if ($validation = $this->is_valid_generate())
            Puzzle::create([
                "start" => implode($this->grid),
                "solution" => implode($validation->grid),
                "difficulty" => $this->grade_difficulty($this->grid),
            ]);
        else
            $this->generate();
    }

    private function symmetric_removal() {
        $index = rand(0, 80);
        $opposite = 80 - $index;
        if ($this->grid[$index] > 0) {
            $this->grid[$index] = 0;
            $this->grid[$opposite] = 0;
            return true;
        }
        return false;
    }

    private function individual_removal() {
        $index = rand(0, 80);
        if ($this->grid[$index] > 0) {
            $this->grid[$index] = 0;
            return true;
        }
        return false;
    }

    private function is_valid_generate() {
        for ($i=0; $i<10; $i++) {
            $sudoku = new self($this->grid);
            $sudoku->solve();
            if ($i == 0)
                $grid = $sudoku->grid;
            elseif ($sudoku->grid != $grid)
                return null;
        }
        return $sudoku;
    }

    private function grade_difficulty(array $grid): float {
        $total = 0;
        for ($i=0; $i<10; $i++) {
            $sudoku = new self($grid);
            $sudoku->solve();
            $total += $sudoku->moves;
        }
    
        $total /= 10;
        echo "grade $total\n";
        return $total < 53 ? 1 : ( $total > 60 ? 3 : 2 );
    }

}

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Online Sudoku Puzzle</title>
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style>
body {
    font-family: 'Lato', sans-serif;
    color: #000;
}

.sudoku-grid {
    display: table;
    border-collapse: collapse;
    margin: auto;
    margin-top: 20px;
    padding: 0;
    border: 1px solid #666;
}

.keypad {
    display: table;
    margin: auto;
}

.sudoku-grid-row:first-child {
    border-top: 2px solid #666;
}

.sudoku-grid-row:nth-child(3n) {
    border-bottom: 2px solid #666;
}

.sudoku-grid-cell {
    display: table-cell;
    cursor: pointer;
    width: calc(11.111vh / 1.8);
    height: calc(11.111vh / 1.8);
    text-align: center;
    line-height: calc(11.111vh / 1.8);
    border: 1px solid #666;
    box-sizing: border-box;
    font-size: 1.2em;
}

div.sudoku-grid-cell:first-child {
    width: calc(11.112vh / 1.8);
    border-left: 3px solid #666;
}

div.sudoku-grid-cell:nth-child(3n) {
    border-right: 3px solid #666;
}

.highlighted {
    background-color: #eee;
}

.selected {
    background-color: #fffdd0;
}

.fixed {
    color: #666;
    font-weight: bold;
}

.incorrect {
    color: #f00;
}

.popover-title {
    text-align: center;
}

.custom-popover li {
    border: none !important;
    text-align: center;
}

.custom-popover li:nth-child(2) {
    border-top: 1px solid #ccc !important;
}

.custom-popover li:last-child {
    border-top: 1px solid #ccc !important;
}

.new-game-difficulty {
    cursor: pointer;
}

</style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="sudoku-grid">
                @php $i = 0; @endphp
                @foreach (range(1, 9) as $row)
                    <div class="sudoku-grid-row" id>
                    @foreach (range(1, 9) as $col)
                        <div class="sudoku-grid-cell" data-id="{{ $i++ }}" ></div>
                    @endforeach
                    </div>
                @endforeach
                </div>
            </div>

                <div class="keypad">
            <div class="row align-items-center">
            @foreach (range(0, 9) as $value)
                <div class="col">
                    <button class="btn btn-outline-dark mt-4 rounded-0 keypad-button" data-value="{{ $value }}">{!! $value ?: "&ndash;" !!}</button>
                </div>
            @endforeach
            </div>
                </div>
            <div class="row mt-4" style="justify-content: center">
                <div class="col-md-6 col-sm-12">
                    <button id="new-game" class="btn btn-primary w-100" data-toggle="popover" data-placement="top" data-toggle="popover">New Game</button>
                </div>
            </div>
        </div>

        <div id="popover-content" style="display: none;">
            <ul class="list-group custom-popover">
                <li class="list-group-item"><a href="javascript:;" class="new-game-difficulty" id="difficulty_1">Easy</a></li>
                <li class="list-group-item"><a href="javascript:;" class="new-game-difficulty" id="difficulty_2">Medium</a></li>
                <li class="list-group-item"><a href="javascript:;" class="new-game-difficulty" id="difficulty_3">Hard</a></li>
            </ul>
        </div>

        <div id="winning-modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Congratulations</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>You win!</p>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script>
    var game_id = "{{  Cookie::get('game_id') }}";
    var starting_cells = [];
    var selected_cell = -1;
    var is_complete = 0;

    $(document).on("keyup", function(e) {
        if (starting_cells.indexOf(selected_cell) === -1) {
            if (selected_cell >= 0 && e.which > 48 && e.which < 58)
                $("div[data-id=" + selected_cell + "]").text(e.key);
            else if (selected_cell >= 0 && (e.which == 8 || e.which == 46))
                $("div[data-id=" + selected_cell + "]").text("");
            update();
        }
    });

    $(".sudoku-grid-cell").on("click", function(e) {
        if (!is_complete) {
            selected_cell = $(this).data("id");
            highlight();
        }
        e.stopPropagation();
    });

    $(".keypad-button").on("click", function(e) {
        if (starting_cells.indexOf(selected_cell) === -1) {
            if (selected_cell >= 0 && $(this).data("value") > 0)
                $("div[data-id=" + selected_cell + "]").text($(this).data("value"));
            else if (selected_cell >= 0)
                $("div[data-id=" + selected_cell + "]").text("");
            update();
        }
        e.stopPropagation();
    });

    $(document).on("click", ".new-game-difficulty", function(e) {
        if (confirm("Are you sure?")) {
            $(".sudoku-grid-cell").removeClass("selected highlighted").text("");
            $.ajax({
                url: "/ajax/games",
                type: "POST",
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    difficulty: $(this).attr("id")[$(this).attr("id").length - 1],
                },
                success: function(response) {
                    $("[data-toggle='popover']").popover("hide");
                    fill_grid(response);
                }
            });
        }
        e.stopPropagation();
    });

    $(document).on("click", function() {
        selected_cell = -1;
        $(".sudoku-grid-cell").removeClass("selected");
    });

    $.ajax({
        url: "/ajax/games/" + game_id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            fill_grid(response);
        }
    });

    function update() {
        var moves = "";
        for (var i=0; i<81; i++)
            moves += $("div[data-id=" + i + "]").text() || "0";
        $.ajax({
            url: "/ajax/games/" + game_id,
            type: "PUT",
            dataType: "json",
            data: {
                _token: "{{ csrf_token() }}",
                moves: moves,
            },
            success: function(response) {
                fill_grid(response);
            }
        });
        highlight();
    }

    function fill_grid(game) {
        var filled_cells = 0;
        game_id = game.id;
        is_complete = game.is_complete;
        starting_cells = game.starting_cells;
        $(".sudoku-grid-cell").removeClass("fixed incorrect");
        for (i=0; i<game.starting_cells.length; i++)
            $("div[data-id=" + game.starting_cells[i] + "]").addClass("fixed");
        for (i=0; i<game.incorrect_cells.length; i++)
            $("div[data-id=" + game.incorrect_cells[i] + "]").addClass("incorrect");
        for (i=0; i<game.moves.length; i++)
            if (game.moves[i] > 0) {
                filled_cells++;
                $("div[data-id=" + i + "]").text(game.moves[i]);
            }
        if (filled_cells == 81 && game.incorrect_cells.length == 0 && !is_complete) {
            $("#winning-modal").modal("show")
            $.ajax({
                url: "/ajax/games/" + game_id,
                type: "DELETE",
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    is_complete = 1;
                    $(".sudoku-grid-cell").removeClass("selected highlighted");
                }
            });
        }
    }

    function highlight() {
        var cell = $("div[data-id=" + selected_cell + "]");
        $(".sudoku-grid-cell").removeClass("selected highlighted");
        if (cell.text())
            $(".sudoku-grid-cell").each(function() {
                if ($(this).text() == cell.text())
                    $(this).addClass("highlighted");
            });
        cell.addClass("selected");
    }

    $("[data-toggle='popover']").popover({
        html: true,
        content: function() {
            return $("#popover-content").html();
        }
    });

</script>

    </body>
<html>


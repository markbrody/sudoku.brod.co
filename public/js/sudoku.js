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
    $(".sudoku-grid-cell").removeClass("selected highlighted").text("");
    $.ajax({
        url: "/ajax/games",
        type: "POST",
        dataType: "json",
        data: {
            _token: csrf_token,
            difficulty: $(this).attr("id")[$(this).attr("id").length - 1],
        },
        success: function(response) {
            $("[data-toggle='popover']").popover("hide");
            fill_grid(response);
        }
    });
    e.stopPropagation();
});

$(document).on("click", "#cancel-new-game", function(e) {
    $("[data-toggle='popover']").popover("hide");
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
            _token: csrf_token,
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
                _token: csrf_token,
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


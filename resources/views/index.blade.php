<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Online Sudoku Puzzle</title>
        <link rel="icon" href="{{ asset('img/favicon/favicon.ico') }}" sizes="16x16 32x32 48x48 64x64" type="image/vnd.microsoft.icon">
        <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/sudoku.css') }}">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-light">
            <div class="container">
                <span class="navbar-brand">Sudoku</span>
                <button id="new-game" class="btn btn-outline-light" data-toggle="popover" data-placement="bottom" data-toggle="popover">
                    &equiv;
                </button>
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="sudoku-grid">
                @php $i = 0; @endphp
                @foreach (range(1, 9) as $row)
                    <div class="sudoku-grid-row">
                    @foreach (range(1, 9) as $col)
                        <div class="sudoku-grid-cell" data-id="{{ $i++ }}" ></div>
                    @endforeach
                    </div>
                @endforeach
                </div>
            </div>
            <div class="row mt-2">
                <div class="keypad">
                @foreach (range(0, 4) as $value)
                    <div class="keypad-cell">
                        <button class="btn btn-outline-dark mt-2 rounded-0 keypad-button" data-value="{{ $value }}">{!! $value ?: "&#9003;" !!}</button>
                    </div>
                @endforeach
                </div>
            </div>
            <div class="row">
                <div class="keypad">
                @foreach (range(5, 9) as $value)
                    <div class="keypad-cell">
                        <button class="btn btn-outline-dark mt-2 rounded-0 keypad-button" data-value="{{ $value }}">{{ $value }}</button>
                    </div>
                @endforeach
                </div>
            </div>
            <div class="row">
                <div class="ad-container mt-2">
{{--
                    <small>Sponsored Ad</small> 
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    <ins class="adsbygoogle adsense"
                         style="display:block"
                         data-ad-client="ca-pub-1537800573901480"
                         data-ad-slot="3142159506"
                         data-ad-format="auto"
                         data-full-width-responsive="true"></ins>
                    <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
--}}
                </div>
            </div>
        </div>

        <div id="popover-content" style="display: none;">
            <ul class="list-group new-game-popover">
                <li class="list-group-item">
                    <a href="javascript:;" class="text-primary new-game-difficulty" id="difficulty_1">
                        <div class="row text-nowrap">
                            <div class="col-6">Easy</div>
                            <div class="col-6">&starf; &star; &star;</div>
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="javascript:;" class="text-primary new-game-difficulty" id="difficulty_2">
                        <div class="row text-nowrap">
                            <div class="col-6">Medium</div>
                            <div class="col-6">&starf; &starf; &star;</div>
                        </div>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="javascript:;" class="text-primary new-game-difficulty" id="difficulty_3">
                        <div class="row text-nowrap">
                            <div class="col-6">Hard</div>
                            <div class="col-6">&starf; &starf; &starf;</div>
                        </div>
                    </a>
                </li>
                <li class="list-group-item text-center">
                    <a href="javascript:;" class="text-danger cancel-new-game" id="cancel-new-game">
                        Cancel
                    </a>
                </li>
            </ul>
        </div>

        <div id="winning-modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Congratulations!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img src="{{ asset('img/win.jpg') }}" class="w-100">
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
            var csrf_token = "{{ csrf_token() }}";
        </script>
        <script src="{{ asset('js/sudoku.js') }}"></script>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-51049739-3"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-51049739-3');
        </script>
    </body>
<html>


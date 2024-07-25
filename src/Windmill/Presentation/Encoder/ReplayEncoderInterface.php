<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Presentation\Replay;

interface ReplayEncoderInterface
{
    public function encode(Replay $game): string;

    public function decode(string $game): Replay;
}

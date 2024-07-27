<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Game;
use App\Windmill\Move\Move;

interface MoveEncoderInterface
{
    public function encode(Move $move, Game $game): string;

    public function decode(mixed $algebraic, Game $game): Move;
}

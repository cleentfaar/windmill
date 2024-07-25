<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;

interface MoveEncoderInterface
{
    public function encode(AbstractMove $move, Game $game): string;

    public function decode(mixed $algebraic, Game $game): AbstractMove;
}

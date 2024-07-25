<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Position;

interface PieceEncoderInterface
{
    public function encode(AbstractPiece $decodedPiece, Position $position): string;

    public function decode(string $encodedPiece, Color $color): AbstractPiece;
}

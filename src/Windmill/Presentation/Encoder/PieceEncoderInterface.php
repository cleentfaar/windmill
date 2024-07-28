<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece;
use App\Windmill\Position;

interface PieceEncoderInterface
{
    public function encode(Piece $decodedPiece, Position $position): string;

    public function decode(string $encodedPiece, Color $color): Piece;
}

<?php

namespace App\Windmill;

enum PieceType: int
{
    case PAWN = 1;
    case BISHOP = 2;
    case KNIGHT = 3;
    case ROOK = 4;
    case QUEEN = 5;
    case KING = 6;

    public function name()
    {
        return mb_ucfirst(mb_strtolower($this->name));
    }
}

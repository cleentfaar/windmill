<?php

namespace App\Windmill\Piece;

use App\Windmill\Move\MoveCollection;

class PieceHistory
{
    public function __construct(
        public readonly MoveCollection $moves = new MoveCollection(),
        public readonly bool $hasAmbigiousMoves = false
    ) {
    }
}

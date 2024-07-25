<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\Pawn;
use Exception;

class SANMoveEncoder extends AlgebraicMoveEncoder
{
    public function __construct(
        private readonly PieceEncoderInterface $pieceEncoder = new SANPieceEncoder(),
        private readonly DelegatingCalculator  $calculator = new DelegatingCalculator())
    {
        parent::__construct($this->pieceEncoder, $this->calculator);
    }
}

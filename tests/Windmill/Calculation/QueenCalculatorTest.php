<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\QueenCalculator;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Queen;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class QueenCalculatorTest extends AbstractCalculatorTest
{
    protected function fenAndExpectedMovesProvider(): array
    {
        return [
            [
                FENGameEncoder::STANDARD_FEN,
                [],
            ],
            [
                '8/pppppppp/8/8/8/8/8/4Q3 w - d4 0 1',
                ['Qa1', 'Qa5', 'Qb1', 'Qb4', 'Qc1', 'Qc3', 'Qd1', 'Qd2', 'Qe2', 'Qe3', 'Qe4', 'Qe5', 'Qe6', 'Qf1', 'Qf2', 'Qg1', 'Qg3', 'Qh1', 'Qh4', 'Qxe7'],
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new QueenCalculator();
    }

    protected function createPiece(Color $color): AbstractPiece
    {
        return new Queen($color);
    }
}

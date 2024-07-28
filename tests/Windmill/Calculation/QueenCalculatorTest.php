<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\QueenCalculator;
use App\Windmill\PieceType;
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
                '3k4/pppppppp/8/8/8/8/8/3KQ3 w - d4 0 1',
                ['Qa5', 'Qb4', 'Qc3', 'Qd2', 'Qe2', 'Qe3', 'Qe4', 'Qe5', 'Qe6', 'Qf1', 'Qf2', 'Qg1', 'Qg3', 'Qh1', 'Qh4', 'Qxe7+'],
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new QueenCalculator();
    }

    protected function getPieceType(): PieceType
    {
        return PieceType::QUEEN;
    }
}

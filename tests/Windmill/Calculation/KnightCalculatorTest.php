<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\KnightCalculator;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Knight;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class KnightCalculatorTest extends AbstractCalculatorTest
{
    protected function fenAndExpectedMovesProvider(): array
    {
        return [
            [
                FENGameEncoder::STANDARD_FEN,
                ['Na3', 'Nc3'],
                Position::B1,
            ],
            [
                'rnbq1rk1/pp2ppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R b KQ e4 0 8',
                ['Nbd7', 'Na6'],
                Position::B8,
            ],
            [
                'rnbq1rk1/pp2ppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R b KQ e4 0 8',
                ['Nfd7', 'Nd5', 'Nxe4', 'Ng4', 'Nh5', 'Ne8'],
                Position::F6,
            ],
            [
                'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q4N2/P3nPPP/3R1K1R b KQ e4 3 20',
                ['Nc1+', 'Nc3+', 'Nf4+', 'Ng1+', 'Ng3+', 'Nxd4+'],
                Position::E2,
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new KnightCalculator();
    }

    protected function createPiece(Color $color): AbstractPiece
    {
        return new Knight($color);
    }
}

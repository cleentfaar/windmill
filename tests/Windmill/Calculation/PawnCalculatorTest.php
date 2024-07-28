<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\PawnCalculator;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Pawn;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class PawnCalculatorTest extends AbstractCalculatorTest
{
    protected function fenAndExpectedMovesProvider(): array
    {
        return [
            'game start' => [
                FENGameEncoder::STANDARD_FEN,
                ['a3', 'a4', 'b3', 'b4', 'c3', 'c4', 'd3', 'd4', 'e3', 'e4', 'f3', 'f4', 'g3', 'g4', 'h3', 'h4'],
            ],
            'capture' => [
                '4k3/8/3p4/4P3/8/8/8/4K3 w - - 0 1',
                ['e6', 'exd6'],
            ],
            'en passant' => [
                '4k3/8/8/3pP3/8/8/8/4K3 w - d6 0 1',
                ['e6', 'exd6'],
            ],
            'check king' => [
                '8/8/8/4k3/8/3P4/8/4K3 w - - 0 1',
                ['d4+'],
                Position::D3,
            ],
            'debug' => [
                'r1bqkb1r/1p3ppp/p1n1pn2/3p4/2P1P3/N1N5/PP3PPP/R1BQKB1R w KQkq - 0 9',
                ['c5', 'cxd5'],
                Position::C4,
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new PawnCalculator();
    }

    protected function createPiece(Color $color): AbstractPiece
    {
        return new Pawn($color);
    }
}

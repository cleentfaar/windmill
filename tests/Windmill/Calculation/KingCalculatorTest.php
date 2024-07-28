<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\KingCalculator;
use App\Windmill\PieceType;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class KingCalculatorTest extends AbstractCalculatorTest
{
    protected function fenAndExpectedMovesProvider(): array
    {
        return [
            'game start' => [
                FENGameEncoder::STANDARD_FEN,
                [],
            ],
            'king capture' => [
                'b2r3r/4Rp1p/pk1q1np1/Np1P4/3Q4/P4PPB/1PP4P/1K6 b - - 0 26',
                ['Kxa5', 'Ka7', 'Kb7', 'Kc5', 'Kc6', 'Kc7'],
            ],
            'castling kingside' => [
                '4k3/pppppppp/8/8/8/8/8/4K2R w KQ d4 0 1',
                ['Ke2', 'Kd1', 'Kf1', 'Kd2', 'Kf2', '0-0'],
            ],
            'castling queenside' => [
                '4k3/pppppppp/8/8/8/8/8/R3K3 w KQ - 0 1',
                ['Ke2', 'Kd1', 'Kf1', 'Kd2', 'Kf2', '0-0-0'],
            ],
        ];
    }

    protected function fenAndExpectedMoveWithOutcomeProvider(): array
    {
        return [
            'castling queenside' => [
                'r2qk2r/pb1n1p1p/2pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/R3KB1R w KQkq e6 0 11',
                '0-0-0',
                'r2qk2r/pb1n1p1p/2pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/2KR1B1R b kq - 1 11',
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new KingCalculator();
    }

    protected function getPieceType(): PieceType
    {
        return PieceType::KING;
    }
}

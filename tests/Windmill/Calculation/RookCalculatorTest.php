<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\RookCalculator;
use App\Windmill\PieceType;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class RookCalculatorTest extends AbstractCalculatorTest
{
    protected function fenAndExpectedMovesProvider(): array
    {
        return [
            'game start' => [
                FENGameEncoder::STANDARD_FEN,
                [],
            ],
            'move multiple squares vertically and horizontally' => [
                '4k3/8/8/8/3R4/8/8/4K3 w - d4 0 1',
                ['Ra4', 'Rb4', 'Rc4', 'Rd1', 'Rd2', 'Rd3', 'Rd5', 'Rd6', 'Rd7', 'Rd8+', 'Re4+', 'Rf4', 'Rg4', 'Rh4'],
            ],
            'capture across multiple squares vertically and horizontally' => [
                '4k3/3p4/8/8/3R3p/8/8/4K3 w - d4 0 1',
                ['Ra4', 'Rb4', 'Rc4', 'Rd1', 'Rd2', 'Rd3', 'Rd5', 'Rd6', 'Rxd7', 'Re4+', 'Rf4', 'Rg4', 'Rxh4'],
            ],
            'weird' => [
                'r1bq1rk1/pp1nppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R w KQ - 1 9',
                ['Rb1', 'Rc1', 'Rd1'],
                Position::A1,
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new RookCalculator();
    }

    protected function getPieceType(): PieceType
    {
        return PieceType::ROOK;
    }
}

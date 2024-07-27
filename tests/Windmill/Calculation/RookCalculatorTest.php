<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\RookCalculator;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Rook;
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
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new RookCalculator();
    }

    protected function createPiece(Color $color): AbstractPiece
    {
        return new Rook($color);
    }
}

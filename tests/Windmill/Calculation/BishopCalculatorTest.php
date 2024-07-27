<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\BishopCalculator;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Bishop;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class BishopCalculatorTest extends AbstractCalculatorTest
{
    protected function fenAndExpectedMovesProvider(): array
    {
        return [
            'game start' => [
                FENGameEncoder::STANDARD_FEN,
                [],
            ],
            'move multiple squares diagonally' => [
                '4k3/8/8/8/8/8/4B3/4K2R w KQ d4 0 1',
                ['Bd3', 'Bc4', 'Bb5+', 'Ba6', 'Bf3', 'Bg4', 'Bh5+', 'Bf1', 'Bd1'],
            ],
            'capture across multiple squares diagonally' => [
                '4k3/8/8/pppppppp/8/8/4B3/4K2R w KQ d4 0 1',
                ['Bd3', 'Bc4', 'Bxb5+', 'Bf3', 'Bg4', 'Bxh5+', 'Bf1', 'Bd1'],
            ],
        ];
    }

    protected function createCalculator(): AbstractPieceCalculator
    {
        return new BishopCalculator();
    }

    protected function createPiece(Color $color): AbstractPiece
    {
        return new Bishop($color);
    }
}

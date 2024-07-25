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
				['Qd1', 'Qc1', 'Qb1', 'Qa1', 'Qd2', 'Qc3', 'Qb4', 'Qa5', 'Qe2', 'Qe3', 'Qe4', 'Qe5', 'Qe6', 'Qxe7', 'Qf2', 'Qg3', 'Qh4', 'Qa1', 'Qb2', 'Qc3', 'Qd4', 'Qf1', 'Qg1', 'Qh1'],
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

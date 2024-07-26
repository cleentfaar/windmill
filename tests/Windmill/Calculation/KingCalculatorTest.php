<?php

namespace App\Tests\Windmill\Calculation;

use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Calculation\KingCalculator;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\King;
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
			'castling kingside' => [
				'8/pppppppp/8/8/8/8/8/4K2R w KQ d4 0 1',
				['Ke2', 'Kd1', 'Kf1', 'Kd2', 'Kf2', '0-0'],
			],
			'castling queenside' => [
				'8/pppppppp/8/8/8/8/8/R3K3 w KQ d4 0 1',
				['Ke2', 'Kd1', 'Kf1', 'Kd2', 'Kf2', '0-0-0'],
			],
		];
	}

	protected function createCalculator(): AbstractPieceCalculator
	{
		return new KingCalculator();
	}

	protected function createPiece(Color $color): AbstractPiece
	{
		return new King($color);
	}
}

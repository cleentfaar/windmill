<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Tests\AbstractTestCase;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class FENGameEncoderTest extends AbstractTestCase
{
	/**
	 * @dataProvider gameProvider
	 */
	public function testItEncodesToExpectedOutput(
		string $expectedFEN
	): void {
		$game = self::createGameFromFEN($expectedFEN);
		$encoder = new FENGameEncoder();
		$actualFEN = $encoder->encode($game);

		$this->assertEquals($expectedFEN, $actualFEN);
	}

	public function gameProvider()
	{
		return [
			[FENGameEncoder::STANDARD_FEN],
			['rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'],
			['rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq c6 0 2'],
			['rnbqkbnr/pp1ppppp/8/2p5/4P3/5N2/PPPP1PPP/RNBQKB1R b KQkq - 1 2'],
		];
	}
}

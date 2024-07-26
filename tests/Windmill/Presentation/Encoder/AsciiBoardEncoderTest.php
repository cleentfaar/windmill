<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Tests\AbstractTestCase;
use App\Windmill\Presentation\Encoder\AsciiBoardEncoder;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class AsciiBoardEncoderTest extends AbstractTestCase
{
	/**
	 * @dataProvider provideFenAndExpectedAscii
	 */
	public function testEndToEndGames(
		string $FEN,
		string $expectedOutput
	): void {
		$game = self::createGameFromFEN($FEN);
		$encoder = new AsciiBoardEncoder(false);
		$actualOutput = $encoder->encode($game->board);

		$this->assertEquals($expectedOutput, $actualOutput);
	}

	public function provideFenAndExpectedAscii()
	{
		return [
			[
				FENGameEncoder::STANDARD_FEN,
				'
8  ♜  ♞  ♝  ♛  ♚  ♝  ♞  ♜ 
7  ♟︎  ♟︎  ♟︎  ♟︎  ♟︎  ♟︎  ♟︎  ♟︎ 
6                         
5                         
4                         
3                         
2  ♙  ♙  ♙  ♙  ♙  ♙  ♙  ♙ 
1  ♖  ♘  ♗  ♕  ♔  ♗  ♘  ♖ 
   A  B  C  D  E  F  G  H 
',
			],
		];
	}
}

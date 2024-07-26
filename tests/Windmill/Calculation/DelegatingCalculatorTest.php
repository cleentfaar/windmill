<?php

namespace App\Tests\Windmill\Calculation;

use App\Tests\AbstractTestCase;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class DelegatingCalculatorTest extends AbstractTestCase
{
	/**
	 * @dataProvider gameStateProvider
	 */
	public function testPossibleMovesPerFEN(
		string $FEN,
		array $expectedMoves
	): void {
		$calculator = new DelegatingCalculator();
		$game = self::createGameFromFEN($FEN);
		$moveCollection = $calculator->calculate($game);

		$this->assertEqualMoves(
			$expectedMoves,
			self::encodeMovesToSANs($moveCollection, $game),
			$game
		);
	}

	public function gameStateProvider()
	{
		return [
			[
				FENGameEncoder::STANDARD_FEN,
				['Na3', 'Nc3', 'Nf3', 'Nh3', 'a3', 'a4', 'b3', 'b4', 'c3', 'c4', 'd3', 'd4', 'e3', 'e4', 'f3', 'f4', 'g3', 'g4', 'h3', 'h4'],
			],
			[
				'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1',
				['a5', 'a6', 'b5', 'b6', 'c5', 'c6', 'd5', 'd6', 'e5', 'e6', 'f5', 'f6', 'g5', 'g6', 'h5', 'h6', 'Na6', 'Nc6', 'Nf6', 'Nh6'],
			],
			[
				'rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR w KQkq c6 0 2',
				['Na3', 'Nc3', 'Nf3', 'Nh3', 'a3', 'a4', 'b3', 'b4', 'c3', 'c4', 'd3', 'd4', 'e5', 'f3', 'f4', 'g3', 'g4', 'h3', 'h4', 'Ke2', 'Qe2', 'Qf3', 'Qg4', 'Qh5', 'Ne2'],
			],
			[
				'rnbqkbnr/pp1ppppp/8/2p5/4P3/5N2/PPPP1PPP/RNBQKB1R b KQkq - 1 2',
				['a5', 'a6', 'b5', 'b6', 'c4', 'd5', 'd6', 'e5', 'e6', 'f5', 'f6', 'g5', 'g6', 'h5', 'h6', 'Na6', 'Nc6', 'Nf6', 'Nh6', 'Qc7', 'Qb6', 'Qa5'],
			],
			[
				'rnbqk2r/ppppppbp/5np1/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R b KQkq d4 0 4', // castling availability for black
				['Na6', 'Nc6', 'Ne4', 'Ng4', 'a5', 'a6', 'b5', 'b6', 'c5', 'c6', 'd5', 'd6', 'e5', 'e6', 'f5', 'g5', 'h5', 'h6', '0-0', 'Kf8', 'Nh5', 'Nd5', 'Ng8'],
			],
			[
				'r3k2r/8/8/8/8/8/8/R3K2R w KQkq d4 0 4', // castling availability (queenside + kingside) for white
				['Kd1', 'Kd2', 'Ke2', 'Kf1', 'Kf2', '0-0', '0-0-0'],
			],
			[
				'rnbq1rk1/ppp1ppbp/5np1/3p4/2PP1B2/1QN2N2/PP2PPPP/R3KB1R b KQ d5 1 6',
				['a6', 'a5', 'b6', 'b5', 'Nc6', 'Na6', 'c6', 'c5', 'Qe8', 'Qd7', 'Qd6', 'e6', 'e5', 'Ng4', 'Ne4', 'f5', 'g5', 'Kh8', 'h6', 'h5', 'dxc4', 'Nh5', 'Ne8', 'Nbd7', 'Nfd7'],
			],
			[
				'rnbq1rk1/pp2ppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R b KQ e4 0 8',
				['Kh8', 'Na6', 'Nbd7', 'Nd5', 'Ne8', 'Nfd7', 'Ng4', 'Nh5', 'Nxe4', 'Qa5', 'Qb6', 'Qc7', 'Qd5', 'Qd6', 'Qd7', 'Qe8', 'Qxd4', 'a5', 'a6', 'b5', 'b6', 'c5', 'e5', 'e6', 'f5', 'g5', 'h5', 'h6'],
			],
		];
	}
}

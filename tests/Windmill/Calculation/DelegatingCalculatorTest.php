<?php

namespace App\Tests\Windmill\Calculation;

use App\Tests\AbstractTestCase;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\CheckState;
use App\Windmill\Move;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;

class DelegatingCalculatorTest extends AbstractTestCase
{
    /**
     * @dataProvider provideFenWithNextMoveAndExpectedCheckState
     */
    public function testCalculateCheckState(string $fen, Move $move, CheckState $expectedState): void
    {
        $game = self::createGameFromFEN($fen);
        $calculator = new DelegatingCalculator();
        $actualCheckState = $calculator->calculateCheckState($move, $game);

        $this->assertEquals($expectedState, $actualCheckState);
    }

    /**
     * @dataProvider provideFenWithPieceDestinationAndExpectedMoves
     */
    public function testCalculcateUniqueDestination(string $fen, Move $move, array $expectedAlgebraics)
    {
        $game = self::createGameFromFEN($fen);
        $calculator = new DelegatingCalculator();
        $actualCollection = $calculator->calculcatePiecesOfTypeWithSameToButDifferentFrom($move, $game);
        $actualAlgebraics = [];

        foreach ($actualCollection as $move) {
            $actualAlgebraics[] = (new AlgebraicMoveEncoder())->encode($move, $game);
        }

        $this->assertEqualsCanonicalizing($expectedAlgebraics, $actualAlgebraics);
    }

    private function provideFenWithPieceDestinationAndExpectedMoves(): array
    {
        return [
            [
                'r1bq1rk1/pp1nppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R w KQ - 1 9',
                new Move([Position::A1], [Position::D1]),
                [],
            ],
        ];
    }

    private function provideFenWithNextMoveAndExpectedCheckState(): array
    {
        return [
            'none' => [
                'k7/8/1R6/2Q5/8/8/8/8 w - - 0 1',
                new Move([Position::C5], [Position::B5]),
                CheckState::NONE,
            ],
            'check' => [
                'k7/8/1R6/2Q5/8/8/8/8 w - - 0 1',
                new Move([Position::C5], [Position::C6]),
                CheckState::CHECK,
            ],
            'checkmate' => [
                'k7/8/1R6/2Q5/8/8/8/8 w - - 0 1',
                new Move([Position::C5], [Position::A5]),
                CheckState::CHECKMATE,
            ],
        ];
    }
}

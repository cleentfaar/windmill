<?php

namespace App\Tests\Windmill\Calculation;

use App\Tests\AbstractTestCase;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\CheckState;
use App\Windmill\Move;
use App\Windmill\Position;

class DelegatingCalculatorTest extends AbstractTestCase
{
    /**
     * @dataProvider provideFenWithNextMoveAndExpectedCheckState
     */
    public function testCalculcateCheckState(string $fen, Move $move, CheckState $expectedState): void
    {
        $game = self::createGameFromFEN($fen);
        $calculator = new DelegatingCalculator();
        $actualCheckState = $calculator->calculcateCheckState($move, $game);

        $this->assertEquals($expectedState, $actualCheckState);
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

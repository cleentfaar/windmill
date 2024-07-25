<?php

namespace App\Tests\Windmill\Calculation;

use App\Tests\AbstractTestCase;
use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\Color;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Position;

abstract class AbstractCalculatorTest extends AbstractTestCase
{
    /**
     * @dataProvider fenAndExpectedMovesProvider
     */
    public function testItCalculatesExpectedMoves(
        string $fen,
        array $expectedMoves,
        ?Position $currentPosition = null,
    ) {
        $game = self::createGameFromFEN($fen);
        $calculator = $this->createCalculator();
        $moves = new MoveCollection();
        $pos = $currentPosition ?: self::findPieceOnBoard($this->createPiece($game->currentColor())::class, $game->currentColor(), $game->board);

        $calculator->calculate(
            $game,
            $pos,
            $game->currentColor(),
            $moves
        );

        $this->assertEqualMoves(
            $expectedMoves,
            self::encodeMovesToSANs($moves, $game),
            $game
        );
    }

    abstract protected function fenAndExpectedMovesProvider(): array;

    abstract protected function createCalculator(): AbstractPieceCalculator;

    abstract protected function createPiece(Color $color): AbstractPiece;
}

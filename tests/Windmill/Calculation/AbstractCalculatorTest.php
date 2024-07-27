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
        $actualMoveCollection = new MoveCollection();
        $pos = $currentPosition ? [$currentPosition] : self::findPiecesOnBoard($this->createPiece($game->currentColor())::class, $game->currentColor(), $game->board);

        foreach ($pos as $p) {
            $calculator->calculate(
                $game,
                $p,
                $game->currentColor(),
                $actualMoveCollection
            );
        }

        $this->assertEqualMoves(
            $expectedMoves,
            self::encodeMovesToSANs($actualMoveCollection, $game),
            $game
        );
    }

    abstract protected function fenAndExpectedMovesProvider(): array;

    abstract protected function createCalculator(): AbstractPieceCalculator;

    abstract protected function createPiece(Color $color): AbstractPiece;
}

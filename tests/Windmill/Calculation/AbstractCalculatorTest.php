<?php

namespace App\Tests\Windmill\Calculation;

use App\Tests\AbstractTestCase;
use App\Windmill\Calculation\AbstractPieceCalculator;
use App\Windmill\MoveCollection;
use App\Windmill\PieceType;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

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
        $pos = $currentPosition ? [$currentPosition] : self::findPiecesOnBoard($this->getPieceType(), $game->currentColor(), $game->board);

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

    /**
     * @dataProvider fenAndExpectedMoveWithOutcomeProvider
     */
    public function testItCalculatesExpectedMoveWithOutcome(
        string $fen,
        string $algebraicMove,
        string $expectedFen,
    ) {
        $game = self::createGameFromFEN($fen);
        $move = (new AlgebraicMoveEncoder())->decode($algebraicMove, $game);
        $game->move($move);

        $this->assertEquals(
            $expectedFen,
            (new FENGameEncoder())->encode($game),
        );
    }

    abstract protected function fenAndExpectedMovesProvider(): array;

    protected function fenAndExpectedMoveWithOutcomeProvider(): array
    {
        return [];
    }

    abstract protected function createCalculator(): AbstractPieceCalculator;

    abstract protected function getPieceType(): PieceType;
}

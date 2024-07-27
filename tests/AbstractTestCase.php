<?php

namespace App\Tests;

use App\Windmill\Board;
use App\Windmill\Color;
use App\Windmill\Engine\Random;
use App\Windmill\Game;
use App\Windmill\GameFactory;
use App\Windmill\MoveCollection;
use App\Windmill\Player;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;
use App\Windmill\Presentation\Encoder\AsciiBoardEncoder;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected static function createGameFromFEN(string $FEN, ?string $engine = null): Game
    {
        return GameFactory::createFromFEN(
            new Player(Color::WHITE, 'tester1', new Random()),
            new Player(Color::BLACK, 'tester2', new Random()),
            $FEN,
        );
    }

    protected function assertEqualMoves(array $expected, array $actual, Game $game): void
    {
        $renderer = new AsciiBoardEncoder(true, true);
        $buffer = $renderer->encode($game->board);
        sort($expected);
        sort($actual);

        $this->assertSame(array_diff($expected, $actual), array_diff($actual, $expected), $buffer);
        $this->assertEqualsCanonicalizing(
            $expected,
            $actual,
            $buffer
        );
    }

    protected static function findPiecesOnBoard(string $class, Color $currentColor, Board $board): array
    {
        return $board->squaresWithPiece($class, $currentColor);
    }

    protected static function encodeMovesToSANs(MoveCollection $moveCollection, Game $game): array
    {
        $encoder = new AlgebraicMoveEncoder();
        $moves = [];

        foreach ($moveCollection->all() as $move) {
            $moves[] = $encoder->encode($move, $game);
        }

        return $moves;
    }
}

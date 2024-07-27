<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\Move;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Position;

class BishopCalculator extends AbstractPieceCalculator
{
    public function calculate(
        Game $game,
        Position $currentPosition,
        Color $currentColor,
        MoveCollection $moves
    ): void {
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board);

        $movements = [
            function (BoardWalker $walker) { return $walker->forwardLeft(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->forwardRight(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->backwardRight(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->backwardLeft(1, true)->current(); },
        ];

        foreach ($movements as $movement) {
            for ($x = 0; $x < 8; ++$x) {
                if ($current = $movement($walker)) {
                    if ($piece = $game->board->pieceOn($current)) {
                        if ($piece->color != $currentColor) {
                            $moves->add(new Move([$walker->startingPosition, $current], [$current, null]));
                        }

                        break;
                    }

                    $moves->add(new Move([$walker->startingPosition], [$current]));
                }
            }

            $walker->reset();
        }
    }
}

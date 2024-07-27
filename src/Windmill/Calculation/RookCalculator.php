<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Position;

class RookCalculator extends AbstractPieceCalculator
{
    public function calculate(
        Game $game,
        Position $currentPosition,
        Color $currentColor,
        MoveCollection $moves
    ): void {
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board);

        $movements = [
            function (BoardWalker $walker) { return $walker->left(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->forward(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->right(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->backward(1, true)->current(); },
        ];

        foreach ($movements as $movement) {
            for ($x = 0; $x < 8; ++$x) {
                if ($current = $movement($walker)) {
                    if ($piece = $game->board->pieceOn($current)) {
                        if ($piece->color != $currentColor) {
                            $moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
                        }

                        break;
                    }

                    $moves->add(new SimpleMove($walker->startingPosition, $current));
                }
            }

            $walker->reset();
        }
    }
}

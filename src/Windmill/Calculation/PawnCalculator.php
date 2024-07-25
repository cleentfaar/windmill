<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Position;

class PawnCalculator extends AbstractPieceCalculator
{
    public function calculate(
        Game           $game,
        Position       $currentPosition,
        Color          $currentColor,
        MoveCollection &$moveCollection
    ): void {
        $this->calculateSimpleMoves($game, $moveCollection, $currentPosition, $currentColor);
        $this->calculcateCaptures($game, $moveCollection, $currentPosition, $currentColor);
    }

    private function calculateSimpleMoves(
        Game $game,
        MoveCollection $moveCollection,
        Position $currentPosition,
        Color $currentColor
    ): void {
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board, true);
        $to = $walker->forward(1)->current();

        if ($to && !$game->board->pieceOn($to)) {
            $moveCollection->add(new SimpleMove($currentPosition, $to));
        }

        $walker->reset();

        if ($this->distanceToBaseline($currentPosition, $currentColor) == 2) {
            // allow double forward
            $to = $walker->forward(2)->current();

            if ($to && !$game->board->pieceOn($to)) {
                $m = new SimpleMove($currentPosition, $to);
                $moveCollection->add($m);
            }
        }
    }

    private function calculcateCaptures(
        Game $game,
        MoveCollection $moveCollection,
        Position $currentPosition,
        Color $currentColor,
    ): void {
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board, false, true);
        $forwardLeft = $walker->forwardLeft(1, true)->current();
        if ($forwardLeft) {
            $opponentPiece = $game->board->pieceOn($forwardLeft);

            if ($opponentPiece && $opponentPiece->color !== $currentColor) {
                $moveCollection->add(new MultiMove([$currentPosition, $forwardLeft], [$forwardLeft, null]));
            }
        }

        $walker->reset();

        $forwardRight = $walker->forwardRight(1, true)->current();

        if ($forwardRight) {
            $opponentPiece = $game->board->pieceOn($forwardRight);

            if ($opponentPiece && $opponentPiece->color !== $currentColor) {
                $moveCollection->add(new MultiMove([$currentPosition, $forwardRight], [$forwardRight, null]));
            }
        }

        $walker->reset();
    }
}

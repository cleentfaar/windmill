<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\Move;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Position;

class PawnCalculator extends AbstractPieceCalculator
{
    public function calculate(
        Game $game,
        Position $currentPosition,
        Color $currentColor,
        MoveCollection $moves
    ): void {
        $this->calculateSimpleMoves($game, $moves, $currentPosition, $currentColor);
        $this->calculcateCaptures($game, $moves, $currentPosition, $currentColor);
        $this->calculcateEnPassant($game, $moves, $currentPosition, $currentColor);
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
            $moveCollection->add(new Move([$currentPosition], [$to]));
        }

        $walker->reset();

        if (2 == $this->distanceToBaseline($currentPosition, $currentColor)) {
            // allow double forward
            $to = $walker->forward(2)->current();

            if ($to && !$game->board->pieceOn($to)) {
                $m = new Move([$currentPosition], [$to]);
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
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board);
        $forwardLeft = $walker->forwardLeft(1, true)->current();
        if ($forwardLeft) {
            $opponentPiece = $game->board->pieceOn($forwardLeft);

            if ($opponentPiece && $opponentPiece->color !== $currentColor) {
                $moveCollection->add(new Move([$currentPosition, $forwardLeft], [$forwardLeft, null]));
            }
        }

        $walker->reset();

        $forwardRight = $walker->forwardRight(1, true)->current();

        if ($forwardRight) {
            $opponentPiece = $game->board->pieceOn($forwardRight);

            if ($opponentPiece && $opponentPiece->color !== $currentColor) {
                $moveCollection->add(new Move([$currentPosition, $forwardRight], [$forwardRight, null]));
            }
        }

        $walker->reset();
    }

    private function calculcateEnPassant(
        Game $game,
        MoveCollection $moveCollection,
        Position $currentPosition,
        Color $currentColor
    ): void {
        if (!$enPassantTarget = $game->enPassantTargetSquare()) {
            return;
        }

        $enPassantCaptures = [
            function ($walker) { return $walker->forwardLeft()->current(); },
            function ($walker) { return $walker->forwardRight()->current(); },
        ];

        foreach ($enPassantCaptures as $enPassantCapture) {
            $walker = new BoardWalker($currentPosition, $currentColor, $game->board);
            $destination = $enPassantCapture($walker);

            if ($destination == $enPassantTarget) {
                $enPassantWalker = new BoardWalker($enPassantTarget, Color::oppositeOf($currentColor), $game->board);
                $positionOfPieceToCapture = $enPassantWalker->forward(1, false, true)->current();
                $moveCollection->add(new Move([$currentPosition, $positionOfPieceToCapture], [$enPassantTarget, null]));
            }
        }
    }
}

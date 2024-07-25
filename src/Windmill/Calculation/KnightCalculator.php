<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Position;

class KnightCalculator extends AbstractPieceCalculator
{
    public function calculate(
        Game           $game,
        Position       $currentPosition,
        Color          $currentColor,
        MoveCollection &$moveCollection
    ): void {
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board, true, false);

        if ($pos = $walker->forward(1, true)->forwardLeft(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();

        if ($pos = $walker->left(1, true)->forwardLeft(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();

        if ($pos = $walker->forward(1, true)->forwardRight(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();

        if ($pos = $walker->right(1, true)->forwardRight(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();
        $buWalker = clone($walker);

        if ($pos = $walker->backward(1, true)->backwardLeft(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $move = new MultiMove(
                    [$walker->startingPosition, $pos],
                    [$pos, null]
                );

                if ($move->from[0] == Position::B8 && $pos == Position::A1) {
                    dump($buWalker->startingPosition);
                    throw new \Exception('Shit');
                }

                $moveCollection->add($move);
            }
        }

        $walker->reset();

        if ($pos = $walker->left(1, true)->backwardLeft(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();

        if ($pos = $walker->backward(1, true)->backwardRight(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();

        if ($pos = $walker->right(1, true)->backwardRight(1, true)->current()) {
            $targetPiece = $game->board->pieceOn($pos);

            if (!$targetPiece) {
                $moveCollection->add(new SimpleMove($currentPosition, $pos));
            } elseif ($targetPiece->color != $currentColor) {
                $moveCollection->add(new MultiMove(
                    [$currentPosition, $pos],
                    [$pos, null]
                ));
            }
        }

        $walker->reset();
    }

    private function determineMove(Position $currentPosition, Position $targetPosition, ?string $opponentPieceClass)
    {
        if ($opponentPieceClass) {
            return new MultiMove([$currentPosition, $targetPosition], [$targetPosition, null]);
        }

        return new SimpleMove($currentPosition, $targetPosition);
    }
}

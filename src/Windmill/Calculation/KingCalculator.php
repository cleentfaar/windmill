<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\MoveCollection;
use App\Windmill\PieceType;
use App\Windmill\Position;

class KingCalculator extends AbstractPieceCalculator
{
    public function calculate(
        Game $game,
        Position $currentPosition,
        Color $currentColor,
        MoveCollection $moves
    ): void {
        $walker = new BoardWalker($currentPosition, $currentColor, $game->board, true);

        $this->calculcateRegularMoves($game, $walker, $moves);
        $this->calculcateCastlingMoves($game, $walker, $moves);
    }

    private function calculcateRegularMoves(Game $game, BoardWalker $walker, MoveCollection $moveCollection)
    {
        $walker->reset();

        $moves = [
            function (BoardWalker $walker) { return $walker->forward(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->forwardRight(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->right(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->backwardRight(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->backward(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->backwardLeft(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->left(1, true)->current(); },
            function (BoardWalker $walker) { return $walker->forwardLeft(1, true)->current(); },
        ];

        foreach ($moves as $move) {
            if ($position = $move($walker)) {
                $capturedPiece = $game->board->pieceOn($position);

                if (!$capturedPiece) {
                    //                    dump('no capture on'.$position->name);
                    $moveCollection->add(new Move([$walker->startingPosition], [$position]));
                } elseif ($capturedPiece->color != $game->currentColor()) {
                    $move1 = new Move([$walker->startingPosition, $position], [$position, null]);
                    //                    dump('adding capture!', $move1);
                    $moveCollection->add($move1);
                }
            }

            $walker->reset();
        }
    }

    private function calculcateCastlingMoves(Game $game, BoardWalker $walker, MoveCollection $moveCollection): void
    {
        if ($game->theoreticallyCanCastleKingside()) {
            $this->calculateKingsideCastlingMove($game, $walker, $moveCollection);
        }

        if ($game->theoreticallyCanCastleQueenside()) {
            $this->calculateQueensideCastlingMove($game, $walker, $moveCollection);
        }
    }

    private function calculateQueensideCastlingMove(Game $game, BoardWalker $walker, MoveCollection $moveCollection): void
    {
        $walker->reset();

        if (!$rookPosition = $walker
            ->absoluteLeft()
            ->absoluteLeft()
            ->absoluteLeft()
            ->absoluteLeft(1, true, true)
            ->current()
        ) {
            return;
        }

        $rook = $game->board->pieceOn($rookPosition);

        if (!$rook || $rook->color != $game->currentColor() || $rook->type !== PieceType::ROOK) {
            return;
        }

        $walker = new BoardWalker($walker->startingPosition, $game->currentColor(), $game->board);
        $moveCollection->add(new Move(
            [$walker->startingPosition, $rookPosition],
            [
                $walker->absoluteLeft(1, true, true)->absoluteLeft(1, true, true)->current(),
                $walker->absoluteRight(1, true, true)->current(),
            ],
            'castle queenside'
        ));

        $walker->reset();
    }

    private function calculateKingsideCastlingMove(Game $game, BoardWalker $walker, MoveCollection $moveCollection): void
    {
        $walker->reset();

        if (!$rookPosition = $walker
            ->absoluteRight()
            ->absoluteRight()
            ->absoluteRight(1, true, true)
            ->current()
        ) {
            return;
        }

        $rook = $game->board->pieceOn($rookPosition);

        if (!$rook || $rook->color != $game->currentColor() || $rook->type !== PieceType::ROOK) {
            return;
        }

        $walker = new BoardWalker($walker->startingPosition, $game->currentColor(), $game->board);
        $moveCollection->add(new Move(
            [$walker->startingPosition, $rookPosition],
            [$walker->absoluteRight(1, true, true)->absoluteRight(1, true, true)->current(), $walker->absoluteLeft(1, true, true)->current()],
            'castle kingside'
        ));

        $walker->reset();
    }
}

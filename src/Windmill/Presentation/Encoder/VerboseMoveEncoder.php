<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\Pawn;
use Exception;

class VerboseMoveEncoder implements MoveEncoderInterface
{
    public function __construct(
        private readonly AlgebraicMoveEncoder  $moveEncoder = new AlgebraicMoveEncoder(),
        private readonly SANPieceEncoder $pieceEncoder = new SANPieceEncoder(),
    ) {
    }

    public function encode(AbstractMove $move, Game $game): string
    {
        $encoded = '';
        $algebraic = $this->moveEncoder->encode($move, $game);

        if (mb_strlen($algebraic) == 2) {
            $encoded .= Pawn::name();
        } else {
            $encoded .= $this->pieceEncoder->decode(
                mb_substr($algebraic, 0, 1),
                $game->currentColor()
            )::name();
        }

        if ($algebraic == '0-0') {
            $encoded .= ' castles kingside';

            return $encoded;
        } elseif ($algebraic == '0-0-0') {
            $encoded .= ' castles queenside';

            return $encoded;
        }

        if (mb_stristr($algebraic, 'x')) {
            $capturedPiece = $game->board->pieceOn($move->to[0]);
            $encoded .= ' takes '.$capturedPiece::class.' on';
        } else {
            $encoded .= ' to';
        }

        switch ($move::class) {
            case SimpleMove::class:
                $encoded .= ' '.$move->to->name;
                break;
            case MultiMove::class:
                $encoded .= ' '.$move->to[0]->name;
                break;
        }

        return $encoded;
    }

    public function decode(mixed $algebraic, Game $game): AbstractMove
    {
    }
}

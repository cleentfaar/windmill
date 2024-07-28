<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\Piece\Pawn;

class VerboseMoveEncoder implements MoveEncoderInterface
{
    public function __construct(
        private readonly AlgebraicMoveEncoder $moveEncoder = new AlgebraicMoveEncoder(),
        private readonly AlgebraicPieceEncoder $pieceEncoder = new AlgebraicPieceEncoder(),
    ) {
    }

    public function encode(Move $move, Game $game): string
    {
        $encoded = '';
        $algebraic = $this->moveEncoder->encode($move, $game);

        if (2 == mb_strlen($algebraic)) {
            $encoded .= Pawn::name();
        } else {
            $encoded .= $this->pieceEncoder->decode(
                mb_substr($algebraic, 0, 1),
                $game->currentColor()
            )::name();
        }

        if ('0-0' == $algebraic) {
            $encoded .= ' castles kingside';

            return $encoded;
        } elseif ('0-0-0' == $algebraic) {
            $encoded .= ' castles queenside';

            return $encoded;
        }

        if (mb_stristr($algebraic, 'x')) {
            $capturedPiece = $game->board->pieceOn($move->primary->to);
            $encoded .= ' takes '.$capturedPiece::name().' on';
        } else {
            $encoded .= ' to';
        }

        $encoded .= ' '.$move->primary->to->name;

        return $encoded;
    }

    public function decode(mixed $algebraic, Game $game): Move
    {
        throw new \Exception(sprintf('Decoding not supported by this class (%s)', __CLASS__));
    }
}

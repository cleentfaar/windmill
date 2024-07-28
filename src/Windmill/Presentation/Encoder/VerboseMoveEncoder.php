<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\PieceType;

class VerboseMoveEncoder implements MoveEncoderInterface
{
    public function __construct(
        private readonly AlgebraicMoveEncoder $moveEncoder = new AlgebraicMoveEncoder(),
        private readonly AlgebraicPieceEncoder $pieceEncoder = new AlgebraicPieceEncoder(),
    ) {
    }

    public function encode(Move $move, Game $game): string
    {
        $algebraic = $this->moveEncoder->encode($move, $game);

        if ($encodedCastling = $this->encodeCastling($algebraic)) {
            $destination = $encodedCastling;
        } else {
            $destination = $this->encodeDestination(
                $algebraic,
                $move,
                $game->board->pieceOn($move->primary->to)?->type
            );
        }

        return sprintf('%s%s',
            $this->encodeMovingPiece($algebraic, $game->currentColor()),
            $destination
        );
    }

    public function decode(mixed $algebraic, Game $game): Move
    {
        throw new \Exception(sprintf('Decoding not supported by this class (%s)', __CLASS__));
    }

    private function encodeMovingPiece(string $algebraic, Color $color)
    {
        if (2 == mb_strlen($algebraic)) {
            return PieceType::PAWN->name();
        }

        return $this->pieceEncoder->decode(
            mb_substr($algebraic, 0, 1),
            $color
        )->type->name();
    }

    private function encodeCastling(string $algebraic): ?string
    {
        if ('0-0' == $algebraic) {
            return ' castles kingside';
        } elseif ('0-0-0' == $algebraic) {
            return ' castles queenside';
        }

        return null;
    }

    private function encodeDestination(string $algebraic, Move $move, ?PieceType $capturedPieceType = null): string
    {
        $encoded = '';

        if (mb_stristr($algebraic, 'x')) {
            $encoded .= ' takes '.$capturedPieceType->name().' on';
        } else {
            $encoded .= ' to';
        }

        $encoded .= ' '.$move->primary->to->name;

        return $encoded;
    }
}

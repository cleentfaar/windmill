<?php

namespace App\Windmill;

class Move
{
    public function __construct(
        public readonly array $from,
        public readonly array $to,
        public readonly string $comment = ''
    ) {
    }

    public function capturesPiecesOfColor(Board $board, Color $color): bool
    {
        foreach ($this->from as $x => $from) {
            $fromPiece = $board->pieceOn($from);

            if ($fromPiece && $fromPiece->color == $color && null == $this->to[$x]) {
                return true;
            }
        }

        return false;
    }
}

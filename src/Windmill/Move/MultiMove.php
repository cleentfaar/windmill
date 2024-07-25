<?php

namespace App\Windmill\Move;

use App\Windmill\Board;
use App\Windmill\Color;

class MultiMove extends AbstractMove
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

            if ($fromPiece && $fromPiece->color == $color && $this->to[$x] == null) {
                return true;
            }
        }

        return false;
    }
}

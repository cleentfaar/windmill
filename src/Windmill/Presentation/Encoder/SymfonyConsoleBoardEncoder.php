<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Position;

class SymfonyConsoleBoardEncoder extends AsciiBoardEncoder
{
    private const string BLACK_SQUARE_BG = '#964B00';
    private const string WHITE_SQUARE_BG = '#DAA06D';
    private const string WHITE_PIECE_FG = '#EADDCA';
    private const string BLACK_PIECE_FG = '#5C4033';

    protected function renderPieceSymbol(Position $position, ?AbstractPiece $piece): string
    {
        $bg = $this->getBackgroundColor($position);
        $fg = $this->getForegroundColor($piece);
        $pieceSymbol = parent::renderPieceSymbol($position, $piece);

        return sprintf('<fg=%s;bg=%s>%s</>', $fg, $bg, $pieceSymbol);
    }

    private function getBackgroundColor(Position $position): string
    {
        if ($position->rank() % 2 == $position->file() % 2) {
            return self::BLACK_SQUARE_BG;
        }

        return self::WHITE_SQUARE_BG;
    }

    private function getForegroundColor(?AbstractPiece $piece): string
    {
        if ($piece && Color::WHITE == $piece->color) {
            return self::WHITE_PIECE_FG;
        }

        return self::BLACK_PIECE_FG;
    }
}

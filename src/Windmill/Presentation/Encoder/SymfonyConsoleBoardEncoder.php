<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Position;

class SymfonyConsoleBoardEncoder extends AsciiBoardEncoder
{
    public function __construct(
        private readonly bool $solidWhite = true,
        private readonly bool $hollowBlack = false,
        private readonly string $spacingCharacter = ' ',
        private readonly string $whiteFg = '#EADDCA',
        private readonly string $whiteBg = '#DAA06D',
        private readonly string $blackFg = '#5C4033',
        private readonly string $blackBg = '#964B00',
    ) {
        parent::__construct(
            $this->solidWhite,
            $this->hollowBlack,
            $this->spacingCharacter
        );
    }

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
            return $this->blackBg;
        }

        return $this->whiteBg;
    }

    private function getForegroundColor(?AbstractPiece $piece): string
    {
        if ($piece && Color::WHITE == $piece->color) {
            return $this->whiteFg;
        }

        return $this->blackFg;
    }
}

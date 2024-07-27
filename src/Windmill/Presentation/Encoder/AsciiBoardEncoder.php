<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Bishop;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Knight;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;
use App\Windmill\Piece\Rook;
use App\Windmill\Position;
use Symfony\Component\Uid\Uuid;

class AsciiBoardEncoder implements BoardEncoderInterface
{
    private const array PIECE_SYMBOLS = [
        Color::WHITE->value => [
            Pawn::class => '♙',
            Bishop::class => '♗',
            Knight::class => '♘',
            Rook::class => '♖',
            Queen::class => '♕',
            King::class => '♔',
        ],
        Color::BLACK->value => [
            Pawn::class => '♟︎',
            Bishop::class => '♝',
            Knight::class => '♞',
            Rook::class => '♜',
            Queen::class => '♛',
            King::class => '♚',
        ],
    ];

    public function __construct(
        private readonly bool $solidWhite = true,
        private readonly bool $hollowBlack = false,
        private readonly string $spacingCharacter = ' '
    ) {
    }

    public function encode(Board $board): string
    {
        $output = "\n";

        for ($rank = 8; $rank >= 1; --$rank) {
            $output .= sprintf('%d ', $rank);
            for ($file = 1; $file <= 8; ++$file) {
                $position = Position::tryFrom($file.$rank);
                $piece = $board->pieceOn($position);
                $output .= $this->renderPieceSymbol($position, $piece);
            }

            $output .= "\n";
        }

        $output .= sprintf(
            "  %s%s%s\n",
            $this->spacingCharacter,
            implode(str_repeat($this->spacingCharacter, 2), range('A', 'H')),
            $this->spacingCharacter
        );

        return $output;
    }

    public function decode(string $encodedBoard, ?Uuid $boardId = null): Board
    {
        // TODO: Implement decode() method.
    }

    protected function renderPieceSymbol(Position $position, ?AbstractPiece $piece)
    {
        return $this->getPieceSymbol($piece);
    }

    protected function getPieceSymbol(?AbstractPiece $piece): string
    {
        if ($piece) {
            if (Color::WHITE == $piece->color && $this->solidWhite) {
                $color = Color::BLACK;
            } elseif (Color::BLACK == $piece->color && $this->hollowBlack) {
                $color = Color::WHITE;
            } else {
                $color = $piece->color;
            }

            return join('', [
                $this->spacingCharacter,
                self::PIECE_SYMBOLS[$color->value][$piece::class],
                $this->spacingCharacter,
            ]);
        }

        return sprintf('%s %s', $this->spacingCharacter, $this->spacingCharacter);
    }
}

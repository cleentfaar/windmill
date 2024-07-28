<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Color;
use App\Windmill\Piece;
use App\Windmill\PieceType;
use App\Windmill\Position;

class AsciiBoardEncoder implements BoardEncoderInterface
{
    private const array PIECE_SYMBOLS = [
        Color::WHITE->value => [
            PieceType::PAWN->value => '♙',
            PieceType::BISHOP->value => '♗',
            PieceType::KNIGHT->value => '♘',
            PieceType::ROOK->value => '♖',
            PieceType::QUEEN->value => '♕',
            PieceType::KING->value => '♔',
        ],
        Color::BLACK->value => [
            PieceType::PAWN->value => '♟︎',
            PieceType::BISHOP->value => '♝',
            PieceType::KNIGHT->value => '♞',
            PieceType::ROOK->value => '♜',
            PieceType::QUEEN->value => '♛',
            PieceType::KING->value => '♚',
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

    protected function renderPieceSymbol(Position $position, ?Piece $piece): string
    {
        return $this->getPieceSymbol($piece);
    }

    protected function getPieceSymbol(?Piece $piece): string
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
                self::PIECE_SYMBOLS[$color->value][$piece->type->value],
                $this->spacingCharacter,
            ]);
        }

        return sprintf('%s %s', $this->spacingCharacter, $this->spacingCharacter);
    }
}

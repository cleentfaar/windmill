<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Bishop;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Knight;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;
use App\Windmill\Piece\Rook;
use App\Windmill\Position;
use Exception;

class FENPieceEncoder implements PieceEncoderInterface
{
    public function encode(AbstractPiece $decodedPiece, Position $position): string
    {
        switch ($decodedPiece::class) {
            case Pawn::class:
                return $decodedPiece->color == Color::WHITE ? 'P' : 'p';
            case Bishop::class:
                return $decodedPiece->color == Color::WHITE ? 'B' : 'b';
            case Knight::class:
                return $decodedPiece->color == Color::WHITE ? 'N' : 'n';
            case Rook::class:
                return $decodedPiece->color == Color::WHITE ? 'R' : 'r';
            case Queen::class:
                return $decodedPiece->color == Color::WHITE ? 'Q' : 'q';
            case King::class:
                return $decodedPiece->color == Color::WHITE ? 'K' : 'k';
            default:
                throw new Exception(sprintf('Unsupported piece: %s', $decodedPiece::class));
        }
    }

    public function decode(string $encodedPiece, Color $color): AbstractPiece
    {
        $color = ctype_upper($encodedPiece) ? Color::WHITE : Color::BLACK;

        switch (mb_strtolower($encodedPiece)) {
            case 'p':
                return new Pawn($color);
            case 'b':
                return new Bishop($color);
            case 'n':
                return new Knight($color);
            case 'r':
                return new Rook($color);
            case 'q':
                return new Queen($color);
            case 'k':
                return new King($color);
            default:
                throw new Exception(sprintf('Unsupported algebraic piece: %s', $encodedPiece));
        }
    }
}

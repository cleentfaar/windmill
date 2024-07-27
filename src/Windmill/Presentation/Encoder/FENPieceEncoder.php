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

class FENPieceEncoder implements PieceEncoderInterface
{
    public function encode(AbstractPiece $decodedPiece, Position $position): string
    {
        switch ($decodedPiece::class) {
            case Pawn::class:
                return Color::WHITE == $decodedPiece->color ? 'P' : 'p';
            case Bishop::class:
                return Color::WHITE == $decodedPiece->color ? 'B' : 'b';
            case Knight::class:
                return Color::WHITE == $decodedPiece->color ? 'N' : 'n';
            case Rook::class:
                return Color::WHITE == $decodedPiece->color ? 'R' : 'r';
            case Queen::class:
                return Color::WHITE == $decodedPiece->color ? 'Q' : 'q';
            case King::class:
                return Color::WHITE == $decodedPiece->color ? 'K' : 'k';
            default:
                throw new \Exception(sprintf('Unsupported piece: %s', $decodedPiece::class));
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
                throw new \Exception(sprintf('Unsupported algebraic piece: %s', $encodedPiece));
        }
    }
}

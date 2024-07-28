<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece;
use App\Windmill\PieceType;
use App\Windmill\Position;

class AlgebraicPieceEncoder implements PieceEncoderInterface
{
    public function encode(Piece $decodedPiece, Position $position): string
    {
        return match ($decodedPiece->type) {
            PieceType::BISHOP => 'B',
            PieceType::KNIGHT => 'N',
            PieceType::ROOK => 'R',
            PieceType::QUEEN => 'Q',
            PieceType::KING => 'K',
            PieceType::PAWN => $position->fileLetter(),
        };
    }

    public function decode(string $encodedPiece, Color $color): Piece
    {
        return match ($encodedPiece) {
            'B' => new Piece($color, PieceType::BISHOP),
            'N' => new Piece($color, PieceType::KNIGHT),
            'R' => new Piece($color, PieceType::ROOK),
            'Q' => new Piece($color, PieceType::QUEEN),
            'K', '0', 'O' => new Piece($color, PieceType::KING),
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' => new Piece($color, PieceType::PAWN),
            default => throw new \Exception(sprintf('Unsupported SAN piece: %s', $encodedPiece)),
        };
    }
}

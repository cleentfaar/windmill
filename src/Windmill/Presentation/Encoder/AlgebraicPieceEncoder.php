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

class AlgebraicPieceEncoder implements PieceEncoderInterface
{
    public function encode(AbstractPiece $decodedPiece, Position $position): string
    {
        return match ($decodedPiece::class) {
            Bishop::class => 'B',
            Knight::class => 'N',
            Rook::class => 'R',
            Queen::class => 'Q',
            King::class => 'K',
            Pawn::class => $position->fileLetter(),
            default => throw new \Exception(sprintf('Unsupported piece: %s', $decodedPiece::class)),
        };
    }

    public function decode(string $encodedPiece, Color $color): AbstractPiece
    {
        return match ($encodedPiece) {
            'B' => new Bishop($color),
            'N' => new Knight($color),
            'R' => new Rook($color),
            'Q' => new Queen($color),
            'K', '0', 'O' => new King($color),
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' => new Pawn($color),
            default => throw new \Exception(sprintf('Unsupported SAN piece: %s', $encodedPiece)),
        };
    }
}

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

class SANPieceEncoder implements PieceEncoderInterface
{
	public function encode(AbstractPiece $decodedPiece, Position $position): string
	{
		switch ($decodedPiece::class) {
			case Bishop::class:
				return 'B';
			case Knight::class:
				return 'N';
			case Rook::class:
				return 'R';
			case Queen::class:
				return 'Q';
			case King::class:
				return 'K';
			case Pawn::class:
				return $position->fileLetter();
			default:
				throw new \Exception(sprintf('Unsupported piece: %s', $decodedPiece::class));
		}
	}

	public function decode(string $encodedPiece, Color $color): AbstractPiece
	{
		switch ($encodedPiece) {
			case 'B':
				return new Bishop($color);
			case 'N':
				return new Knight($color);
			case 'R':
				return new Rook($color);
			case 'Q':
				return new Queen($color);
			case 'K':
			case '0':
			case 'O':
				return new King($color);
			case 'a':
			case 'b':
			case 'c':
			case 'd':
			case 'e':
			case 'f':
			case 'g':
			case 'h':
				return new Pawn($color);
			default:
				throw new \Exception(sprintf('Unsupported SAN piece: %s', $encodedPiece));
		}
	}
}

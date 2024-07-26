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
	private const PIECE_SYMBOLS = [
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

	public function __construct(private readonly bool $solidWhite = true, private readonly bool $hollowBlack = false)
	{
	}

	public function encode(Board $board): string
	{
		$output = "\n";

		for ($rank = 8; $rank >= 1; --$rank) {
			$output .= $rank.' ';
			for ($file = 1; $file <= 8; ++$file) {
				$position = Position::tryFrom($file.$rank);
				$piece = $board->pieceOn($position);
				$output .= $this->renderPieceSymbol($position, $piece);
			}
			$output .= "\n";
		}

		$output .= "   A  B  C  D  E  F  G  H \n";

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

			return sprintf(
				' %s ',
				self::PIECE_SYMBOLS[$color->value][$piece::class]
			);
		}

		return '   ';
	}
}

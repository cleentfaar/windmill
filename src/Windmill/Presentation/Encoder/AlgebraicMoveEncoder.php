<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;

class AlgebraicMoveEncoder implements MoveEncoderInterface
{
	public function __construct(
		private readonly PieceEncoderInterface $pieceEncoder = new SANPieceEncoder(),
		private readonly DelegatingCalculator $calculator = new DelegatingCalculator()
	) {
	}

	public function encode(AbstractMove $move, Game $game): string
	{
		switch ($move::class) {
			case SimpleMove::class:
				$to = $move->to;
				$movingPiece = $game->board->pieceOn($move->from);

				if (!$movingPiece) {
					throw new \Exception(sprintf('There is no piece to move from that position: %s', $move->from->name));
				}

				if (Pawn::class != $movingPiece::class) {
					$firstChar = $this->pieceEncoder->encode($movingPiece, $move->from);
				} else {
					$firstChar = '';
				}

				return sprintf(
					'%s%s%s',
					$firstChar,
					$to->fileLetter(),
					$to->rank()
				);
			case MultiMove::class:
				$movingPiece = $game->board->pieceOn($move->from[0]);

				if (King::class == $movingPiece::class && abs($move->from[0]->file() - $move->to[0]->file()) > 1) {
					// castling
					$jumpSize = abs($move->from[0]->file() - $move->to[0]->file());

					if ($jumpSize > 3) {
						return '0-0-0';
					} else {
						return '0-0';
					}
				}

				$isCapture = $move->capturesPiecesOfColor(
					$game->board,
					Color::oppositeOf($game->currentColor())
				);

				if (!$isCapture) {
					throw new \Exception('Encoder only supports regular capture multi-moves for now');
				}

				if (Pawn::class == $movingPiece::class) {
					$firstChar = $move->from[0]->fileLetter();
				} else {
					$firstChar = $this->pieceEncoder->encode($game->board->pieceOn($move->from[0]), $move->from[0]);
				}

				$moves = $this->calculator->calculateWithDestination($move->to[0], $game)->all();

				if (sizeof($moves) > 1 && !in_array($movingPiece::class, [Queen::class, King::class])) {
					foreach ($moves as $m) {
						switch ($m::class) {
							case SimpleMove::class:
								if ($game->board->pieceOn($m->from)::class == $movingPiece::class && $m->from !== $move->from[0]) {
									$firstChar .= $move->from[0]->fileLetter();
									break 2;
								}

								break;
							case MultiMove::class:
								if ($game->board->pieceOn($m->from[0])::class == $movingPiece::class && $m->from[0] !== $move->from[0]) {
									$firstChar .= $move->from[0]->fileLetter();
									break 2;
								}

								break;
						}
					}
				}

				return sprintf(
					'%sx%s%d',
					$firstChar,
					$move->to[0]->fileLetter(),
					$move->to[0]->rank()
				);
			default:
				throw new \Exception(sprintf("Moves of type '%s' can not be encoded", $move::class));
		}
	}

	public function decode(mixed $algebraic, Game $game): AbstractMove
	{
		$algebraic = str_replace('O', '0', $algebraic);
		$possibleMoves = [];

		foreach ($this->calculator->calculate($game)->all() as $move) {
			$encoded = $this->encode($move, $game);

			if ($encoded == $algebraic) {
				$possibleMoves[$encoded] = $move;
			}
		}

		if (1 != sizeof($possibleMoves)) {
			throw new \Exception(sprintf("Expected exactly one possible move that results into '%s', got %d%s", $algebraic, sizeof($possibleMoves), sizeof($possibleMoves) > 0 ? sprintf(' (%s)', implode(',', array_keys($possibleMoves))) : ''));
		}

		return array_shift($possibleMoves);
	}
}

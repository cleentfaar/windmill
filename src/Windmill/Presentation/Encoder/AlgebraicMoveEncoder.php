<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;
use DeepCopy\DeepCopy;

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

				$moves = $this->calculator->calculateWithDestination($move->to, $game);

				$uniqueFile = $this->encodeUniqueFile(
					$movingPiece,
					$move,
					$moves,
					$game
				);

				$checksOrCheckmates = '';
				//				$checksOrCheckmates = $this->encodeChecksOrCheckmatesOpponent($movingPiece, $move, $game);

				return sprintf(
					'%s%s%s%s%s',
					$firstChar,
					$uniqueFile,
					$to->fileLetter(),
					$to->rank(),
					$checksOrCheckmates
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

				if (Pawn::class == $movingPiece::class) {
					$firstChar = $move->from[0]->fileLetter();
				} else {
					$firstChar = $this->pieceEncoder->encode($game->board->pieceOn($move->from[0]), $move->from[0]);
				}

				$moves = $this->calculator->calculateWithDestination($move->from[0], $game);
				$uniqueFile = $this->encodeUniqueFile($movingPiece, $move, $moves, $game);
				$checksOrCheckmates = '';
				//				$checksOrCheckmates = $this->encodeChecksOrCheckmatesOpponent($movingPiece, $move, $game);

				return sprintf(
					'%s%sx%s%d%s',
					$firstChar,
					$uniqueFile,
					$move->from[1]->fileLetter(),
					$move->from[1]->rank(),
					$checksOrCheckmates
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

	private function encodeUniqueFile(AbstractPiece $movingPiece, AbstractMove $move, MoveCollection $moves, Game $game): string
	{
		$uniqueFile = '';
		$moveFrom = SimpleMove::class == $move::class ? $move->from : $move->from[0];
		$moves = $moves->all();

		if (sizeof($moves) > 1 && !in_array($movingPiece::class, [Queen::class, King::class])) {
			foreach ($moves as $m) {
				$mFrom = SimpleMove::class == $m::class ? $m->from : $m->from[0];
				if ($game->board->pieceOn($mFrom)::class == $movingPiece::class && $mFrom !== $moveFrom) {
					$uniqueFile .= $moveFrom->fileLetter();
					break;
				}
			}
		}

		return $uniqueFile;
	}

	private function encodeChecksOrCheckmatesOpponent(AbstractPiece $movingPiece, AbstractMove $move, Game $game): string
	{
		/* @var Game $clone */
		$clone = (new DeepCopy())->copy($game);
		$clone->move($move, true);
		$squaresWithOppositeKing = $clone->board->squaresWithPiece(King::class, Color::oppositeOf($movingPiece->color));

		if (1 != sizeof($squaresWithOppositeKing)) {
			throw new \Exception(sprintf('Expected a single square to be occupied, got %d', sizeof($squaresWithOppositeKing)));
		}

		$moveCollection = $this->calculator->calculateWithDestination($squaresWithOppositeKing[0], $clone);
		$moveTo = SimpleMove::class == $move::class ? $move->to : $move->to[0];

		foreach ($moveCollection->all() as $m) {
			if ($m->from[0] == $moveTo && $clone->board->pieceOn($m->from[0])::class == $movingPiece::class) {
				return '+';
			}
		}

		return '';
	}
}

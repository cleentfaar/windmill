<?php

namespace App\Windmill\Calculation;

use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\Bishop;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Knight;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;
use App\Windmill\Piece\Rook;
use App\Windmill\Position;

class DelegatingCalculator
{
	/**
	 * @var AbstractPieceCalculator[]
	 */
	private array $calculators;

	public function __construct(array $calculators = [])
	{
		$this->calculators = $calculators ?: [
			Pawn::class => new PawnCalculator(),
			Bishop::class => new BishopCalculator(),
			Knight::class => new KnightCalculator(),
			Rook::class => new RookCalculator(),
			Queen::class => new QueenCalculator(),
			King::class => new KingCalculator(),
		];
	}

	public function calculate(Game $game): MoveCollection
	{
		$currentColor = $game->currentColor();
		$moveCollection = new MoveCollection();

		foreach ($game->board->squares() as $position => $piece) {
			if ($piece && $piece->color == $currentColor) {
				$this->calculators[$piece::class]->calculate(
					$game,
					Position::from($position),
					$piece->color,
					$moveCollection
				);
			}
		}

		return $moveCollection;
	}

	public function calculateWithDestination(Position $to, Game $game)
	{
		$remaining = new MoveCollection();

		foreach ($this->calculate($game)->all() as $move) {
			switch ($move::class) {
				case SimpleMove::class:
					if ($move->to == $to) {
						$remaining->add($move);
					}
					break;
				case MultiMove::class:
					foreach ($move->to as $toInMOve) {
						if ($toInMOve == $to) {
							$remaining->add($move);
						}
					}
					break;
			}
		}

		return $remaining;
	}
}

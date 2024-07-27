<?php

namespace App\Windmill\Calculation;

use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Position;

abstract class AbstractPieceCalculator
{
	abstract public function calculate(
		Game $game,
		Position $currentPosition,
		Color $currentColor,
		MoveCollection $moves
	): void;

	public function distanceToBaseline(Position $position, Color $color): int
	{
		$rank = substr($position->value, 1, 1);

		if (Color::WHITE == $color) {
			return $rank;
		}

		return 9 - $rank;
	}
}

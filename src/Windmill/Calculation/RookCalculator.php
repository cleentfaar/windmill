<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Position;

class RookCalculator extends AbstractPieceCalculator
{
	public function calculate(
		Game $game,
		Position $currentPosition,
		Color $currentColor,
		MoveCollection &$moveCollection
	): void {
		$walker = new BoardWalker($currentPosition, $currentColor, $game->board);
	}
}

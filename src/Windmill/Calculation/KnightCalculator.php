<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Position;

class KnightCalculator extends AbstractPieceCalculator
{
	public function calculate(
		Game $game,
		Position $currentPosition,
		Color $currentColor,
		MoveCollection $moves
	): void {
		$walker = new BoardWalker($currentPosition, $currentColor, $game->board, true, false);
		$lShapes = [
			function () use ($walker) { return $walker->forward(1, false, true)->forwardLeft(1, true, false)->current(); },
			function () use ($walker) { return $walker->left(1, true, true)->forwardLeft(1, true, false)->current(); },
			function () use ($walker) { return $walker->forward(1, true, true)->forwardRight(1, true, false)->current(); },
			function () use ($walker) { return $walker->right(1, true, true)->forwardRight(1, true, false)->current(); },
			function () use ($walker) { return $walker->backward(1, true, true)->backwardLeft(1, true, false)->current(); },
			function () use ($walker) { return $walker->left(1, true, true)->backwardLeft(1, true, false)->current(); },
			function () use ($walker) { return $walker->backward(1, true, true)->backwardRight(1, true, false)->current(); },
			function () use ($walker) { return $walker->right(1, true, true)->backwardRight(1, true, false)->current(); },
		];

		foreach ($lShapes as $posCallback) {
			if ($pos = $posCallback()) {
				$targetPiece = $game->board->pieceOn($pos);

				if (!$targetPiece) {
					$moves->add(new SimpleMove($currentPosition, $pos));
				} elseif ($targetPiece->color != $currentColor) {
					$moves->add(new MultiMove(
						[$currentPosition, $pos],
						[$pos, null]
					));
				}
			}

			$walker->reset();
		}
	}
}

<?php

namespace App\Windmill\Calculation;

use App\Windmill\Board;
use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Position;

class QueenCalculator extends AbstractPieceCalculator
{
	public function calculate(
		Game $game,
		Position $currentPosition,
		Color $currentColor,
		MoveCollection &$moveCollection
	): void {
		$walker = new BoardWalker($currentPosition, $currentColor, $game->board);

		$this->calculateRegularMoves(
			$walker,
			$moveCollection,
			$currentColor,
			$game->board
		);
	}

	private function calculateRegularMoves(BoardWalker $walker, MoveCollection $moves, Color $currentColor, Board $board)
	{
		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->left(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->forwardLeft(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->forward(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->forwardRight(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->right(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->backwardRight(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->backward(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}

		$walker->reset();

		for ($x = 0; $x < 8; ++$x) {
			if ($current = $walker->backwardLeft(1, true)->current()) {
				if ($piece = $board->pieceOn($current)) {
					if ($piece->color != $currentColor) {
						$moves->add(new MultiMove([$walker->startingPosition, $current], [$current, null]));
					}

					break;
				}

				$moves->add(new SimpleMove($walker->startingPosition, $current));
			}
		}
	}
}

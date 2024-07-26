<?php

namespace App\Windmill\Calculation;

use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\Rook;
use App\Windmill\Position;

class KingCalculator extends AbstractPieceCalculator
{
	public function calculate(
		Game $game,
		Position $currentPosition,
		Color $currentColor,
		MoveCollection &$moveCollection
	): void {
		$walker = new BoardWalker($currentPosition, $currentColor, $game->board, true);

		$this->calculcateRegularMoves($game, $walker, $moveCollection);
		$this->calculcateCastlingMoves($game, $walker, $moveCollection);
	}

	private function calculcateCastlingMoves(Game $game, BoardWalker $walker, MoveCollection $moveCollection): void
	{
		if (
			(Color::WHITE == $game->currentColor() && !$game->castlingAvailability->whiteCanCastleQueenside && !$game->castlingAvailability->whiteCanCastleKingside)
			|| (Color::BLACK == $game->currentColor() && !$game->castlingAvailability->blackCanCastleQueenside && !$game->castlingAvailability->blackCanCastleKingside)
		) {
			return;
		}

		$this->calculateQueensideCastlingMove($game, $walker, $moveCollection);
		$this->calculateKingsideCastlingMove($game, $walker, $moveCollection);
	}

	private function calculcateRegularMoves(Game $game, BoardWalker $walker, MoveCollection $moveCollection)
	{
		$walker->reset();

		if ($position = $walker->forward()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->backward()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->left()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->right()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->forwardLeft()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->forwardRight()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->backwardLeft()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}

		$walker->reset();

		if ($position = $walker->backwardRight()->current()) {
			$moveCollection->add(new SimpleMove($walker->startingPosition, $position));
		}
	}

	private function calculateQueensideCastlingMove(Game $game, BoardWalker $walker, MoveCollection $moveCollection): void
	{
		$walker->reset();

		if (!$walker->absoluteLeft()->absoluteLeft()->absoluteLeft()->current()) {
			return;
		}

		$rookPosition = $walker->absoluteLeft(1, false, true)->current();
		$rook = $game->board->pieceOn($rookPosition);

		if (!$rook || $rook->color != $game->currentColor() || Rook::class !== $rook::class) {
			return;
		}

		$moveCollection->add(new MultiMove(
			[$walker->startingPosition, $walker->absoluteRight()->current()],
			[$rookPosition, $walker->absoluteLeft()->current()]
		));
	}

	private function calculateKingsideCastlingMove(Game $game, BoardWalker $walker, MoveCollection $moveCollection): void
	{
		$walker->reset();

		if (!$walker->absoluteRight()->absoluteRight()->current()) {
			return;
		}

		$rookPosition = $walker->absoluteRight(1, false, true)->current();

		if (!$rookPosition) {
			return;
		}

		$rook = $game->board->pieceOn($rookPosition);

		if (!$rook || $rook->color != $game->currentColor() || Rook::class !== $rook::class) {
			return;
		}

		$move = new MultiMove(
			[$walker->startingPosition, $rookPosition],
			[$walker->absoluteLeft(1, true)->current(), $walker->absoluteLeft(1, true)->current()],
			'castle'
		);
		$moveCollection->add($move);

		$walker->reset();
	}
}

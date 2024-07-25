<?php

namespace App\Windmill;

use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;

class BoardWalker
{
	private ?Position $currentPosition;
	private array $recorded = [];

	public function __construct(
		public readonly ?Position $startingPosition,
		private readonly Color $color,
		private readonly Board $board,
		private readonly bool $recording = false,
		private readonly bool $allowIntermittentCollisions = false
	) {
		$this->currentPosition = $this->startingPosition;
	}

	public function reset(): void
	{
		$this->currentPosition = $this->startingPosition;
		$this->recorded = [];
	}

	public function flush(): array
	{
		$recorded = $this->recorded;
		$this->recorded = [];

		return $recorded;
	}

	public function forward(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$r = $this->rank() + $steps;
		} else {
			$r = $this->rank() - $steps;
		}

		return $this->record($this->positionWithCurrentFileAndRank($r), $allowIntermittentCollisions);
	}

	public function forwardRight(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$f = $this->file() + $steps;
			$r = $this->rank() + $steps;
		} else {
			$f = $this->file() - $steps;
			$r = $this->rank() - $steps;
		}

		return $this->record($this->positionWithFileAndRank($f, $r), $allowIntermittentCollisions);
	}

	public function forwardLeft(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$f = $this->file() - $steps;
			$r = $this->rank() + $steps;
		} else {
			$f = $this->file() + $steps;
			$r = $this->rank() - $steps;
		}

		return $this->record($this->positionWithFileAndRank($f, $r), $allowIntermittentCollisions);
	}

	public function backward(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$r = $this->rank() - $steps;
		} else {
			$r = $this->rank() + $steps;
		}

		return $this->record($this->positionWithCurrentFileAndRank($r), $allowIntermittentCollisions);
	}

	public function backwardRight(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$f = $this->file() + $steps;
			$r = $this->rank() - $steps;
		} else {
			$f = $this->file() - $steps;
			$r = $this->rank() + $steps;
		}

		return $this->record($this->positionWithFileAndRank($f, $r), $allowIntermittentCollisions);
	}

	public function backwardLeft(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$f = $this->file() - $steps;
			$r = $this->rank() - $steps;
		} else {
			$f = $this->file() + $steps;
			$r = $this->rank() + $steps;
		}

		return $this->record($this->positionWithFileAndRank($f, $r), $allowIntermittentCollisions);
	}

	public function current(): ?Position
	{
		return $this->currentPosition;
	}

	public function left(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$f = $this->file() - $steps;
		} else {
			$f = $this->file() + $steps;
		}

		return $this->record($this->positionWithFileAndCurrentRank($f), $allowIntermittentCollisions);
	}

	public function right(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		if (Color::WHITE == $this->color) {
			$f = $this->file() + $steps;
		} else {
			$f = $this->file() - $steps;
		}

		return $this->record($this->positionWithFileAndCurrentRank($f), $allowIntermittentCollisions);
	}

	public function absoluteLeft(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		$f = $this->file() - $steps;

		return $this->record($this->positionWithFileAndCurrentRank($f), $allowIntermittentCollisions);
	}

	public function absoluteRight(int $steps = 1, ?bool $allowIntermittentCollisions = null): self
	{
		$f = $this->file() + $steps;
		$position = $this->positionWithFileAndCurrentRank($f);

		return $this->record($position, $allowIntermittentCollisions);
	}

	public function diagonals(MoveCollection $moveCollection): self
	{
		$startingColor = $this->board->pieceOn($this->startingPosition)->color;

		while ($pos = $this->forwardLeft()->current()) {
			if ($piece = $this->board->pieceOn($pos)) {
				if ($piece->color !== $startingColor) {
					$moveCollection->add(new MultiMove($this->startingPosition, $pos, $piece::class));
				}

				break;
			} else {
				$moveCollection->add(new SimpleMove($this->startingPosition, $pos));
			}
		}

		$this->reset();

		while ($pos = $this->forwardRight()->current()) {
			if ($piece = $this->board->pieceOn($pos)) {
				if ($piece->color !== $startingColor) {
					$moveCollection->add(new MultiMove($this->startingPosition, $pos, $piece::class));
				}

				break;
			} else {
				$moveCollection->add(new SimpleMove($this->startingPosition, $pos));
			}
		}

		while ($pos = $this->backwardLeft()->current()) {
			if ($piece = $this->board->pieceOn($pos)) {
				if ($piece->color !== $startingColor) {
					$moveCollection->add(new MultiMove($this->startingPosition, $pos, $piece::class));
				}

				break;
			} else {
				$moveCollection->add(new SimpleMove($this->startingPosition, $pos));
			}
		}

		$this->reset();

		while ($pos = $this->backwardRight()->current()) {
			if ($piece = $this->board->pieceOn($pos)) {
				if ($piece->color !== $startingColor) {
					$moveCollection->add(new MultiMove($this->startingPosition, $pos, $piece::class));
				}

				break;
			} else {
				$moveCollection->add(new SimpleMove($this->startingPosition, $pos));
			}
		}

		$this->reset();

		return $this;
	}

	private function rank(): ?int
	{
		if (!$this->currentPosition) {
			return null;
		}

		return substr($this->currentPosition->value, 1, 1);
	}

	private function file(): ?int
	{
		if (!$this->currentPosition) {
			return null;
		}

		return substr($this->currentPosition->value, 0, 1);
	}

	private function positionWithFileAndRank(int $file, int $rank): ?Position
	{
		return Position::fromFileAndRank($file, $rank);
	}

	private function positionWithFileAndCurrentRank(int $file): ?Position
	{
		return Position::tryFrom($file.$this->rank());
	}

	private function positionWithCurrentFileAndRank(int $rank): ?Position
	{
		return Position::tryFrom($this->file().$rank);
	}

	private function record(?Position $position, ?bool $allowIntermittentCollisions = null): self
	{
		if ($position && (
			(!$this->allowIntermittentCollisions && (null == $allowIntermittentCollisions || false == $allowIntermittentCollisions))
			&& $this->board->pieceOn($position)
		)
		) {
			$position = null;
		}

		if ($this->recording) {
			if ($position) {
				$this->recorded[] = $position;
			}
		}

		$this->currentPosition = $position;

		return $this;
	}
}

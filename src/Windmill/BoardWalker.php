<?php

namespace App\Windmill;

class BoardWalker
{
    private array $positions = [];
    private ?Position $currentPosition;

    public function __construct(
        public readonly Position $startingPosition,
        private readonly Color $color,
        private readonly Board $board,
    ) {
        $this->currentPosition = $this->startingPosition;
    }

    public function reset(): self
    {
        $this->positions = [];
        $this->currentPosition = $this->startingPosition;

        return $this;
    }

    public function current(): ?Position
    {
        return $this->currentPosition;
    }

    public function flush(): array
    {
        $steps = $this->positions;
        $this->positions = [];

        return $steps;
    }

    public function forward(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $r = $this->rank() + $steps;
        } else {
            $r = $this->rank() - $steps;
        }

        return $this->record(
            $this->positionWithCurrentFileAndRank($r),
            $allowCapture,
            $allowIntermittentCollisions
        );
    }

    public function forwardRight(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $f = $this->file() + $steps;
            $r = $this->rank() + $steps;
        } else {
            $f = $this->file() - $steps;
            $r = $this->rank() - $steps;
        }

        return $this->record($this->positionWithFileAndRank($f, $r), $allowCapture, $allowIntermittentCollisions);
    }

    public function forwardLeft(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $f = $this->file() - $steps;
            $r = $this->rank() + $steps;
        } else {
            $f = $this->file() + $steps;
            $r = $this->rank() - $steps;
        }

        return $this->record($this->positionWithFileAndRank($f, $r), $allowCapture, $allowIntermittentCollisions);
    }

    public function backward(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $r = $this->rank() - $steps;
        } else {
            $r = $this->rank() + $steps;
        }

        return $this->record($this->positionWithCurrentFileAndRank($r), $allowCapture, $allowIntermittentCollisions);
    }

    public function backwardRight(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $f = $this->file() + $steps;
            $r = $this->rank() - $steps;
        } else {
            $f = $this->file() - $steps;
            $r = $this->rank() + $steps;
        }

        return $this->record($this->positionWithFileAndRank($f, $r), $allowCapture, $allowIntermittentCollisions);
    }

    public function backwardLeft(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $f = $this->file() - $steps;
            $r = $this->rank() - $steps;
        } else {
            $f = $this->file() + $steps;
            $r = $this->rank() + $steps;
        }

        return $this->record($this->positionWithFileAndRank($f, $r), $allowCapture, $allowIntermittentCollisions);
    }

    public function left(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $f = $this->file() - $steps;
        } else {
            $f = $this->file() + $steps;
        }

        return $this->record($this->positionWithFileAndCurrentRank($f), $allowCapture, $allowIntermittentCollisions);
    }

    public function right(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        if (Color::WHITE == $this->color) {
            $f = $this->file() + $steps;
        } else {
            $f = $this->file() - $steps;
        }

        return $this->record($this->positionWithFileAndCurrentRank($f), $allowCapture, $allowIntermittentCollisions);
    }

    public function absoluteLeft(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        $f = $this->file() - $steps;

        return $this->record($this->positionWithFileAndCurrentRank($f), $allowCapture, $allowIntermittentCollisions);
    }

    public function absoluteRight(int $steps = 1, bool $allowCapture = false, bool $allowIntermittentCollisions = false): self
    {
        if (!$this->currentPosition) {
            return $this;
        }

        $f = $this->file() + $steps;
        $position = $this->positionWithFileAndCurrentRank($f);

        return $this->record($position, $allowCapture, $allowIntermittentCollisions);
    }

    public function rank(): int
    {
        return $this->currentPosition->rank();
    }

    public function file(): int
    {
        return $this->currentPosition->file();
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

    private function record(?Position $position, bool $allowCapture, bool $allowIntermittentCollisions): self
    {
        $this->currentPosition = null;

        if (!$position) {
            return $this;
        }

        $piece = $this->board->pieceOn($position);

        if (
            !$piece
            || (
                $piece
                && (
                    ($allowCapture && $piece->color !== $this->color)
                    || $allowIntermittentCollisions
                )
            )
        ) {
            $this->positions[] = $position;
            $this->currentPosition = $position;

            return $this;
        }

        return $this;
    }
}

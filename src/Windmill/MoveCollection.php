<?php

namespace App\Windmill;

class MoveCollection
{
    public function __construct(private array $moves = [])
    {
    }

    public function add(Move $move): void
    {
        $this->moves[] = $move;
    }

    public function to(Position $to): MoveCollection
    {
        return new MoveCollection(array_filter($this->moves, function (Move $m) use ($to) {
            return in_array(
                $to,
                $m->to
            );
        }));
    }

    public function filter(callable $callback): MoveCollection
    {
        return new MoveCollection(array_filter($this->moves, $callback));
    }

    public function all(): array
    {
        return $this->moves;
    }
}

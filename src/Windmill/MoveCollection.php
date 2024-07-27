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

    public function from(Position $position): array
    {
        return array_filter($this->moves, function (Move $m) use ($position) {
            return in_array(
                $position->value,
                array_map(
                    function (Position $from) {
                        return $from->value;
                    },
                    $m->from()
                )
            );
        });
    }

    public function all(): array
    {
        return $this->moves;
    }
}
<?php

namespace App\Windmill\Move;

use App\Windmill\Position;

class MoveCollection
{
    public function __construct(private array $moves = [])
    {
    }

    public function add(AbstractMove $move): void
    {
        $this->moves[] = $move;
    }

    public function from(Position $position): array
    {
        return array_filter($this->moves, function (AbstractMove $m) use ($position) {
            return in_array(
                $position->value,
                array_map(
                    function(Position $from) {
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

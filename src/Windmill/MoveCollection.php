<?php

namespace App\Windmill;

class MoveCollection implements \IteratorAggregate, \Countable
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

    public function fromFile(int $file): MoveCollection
    {
        return new MoveCollection(array_filter($this->moves, function (Move $m) use ($file) {
            foreach ($m->from as $from) {
                if ($from->file() == $file) {
                    return true;
                }
            }
        }));
    }

    public function pickRandom(): ?Move
    {
        $key = array_rand($this->moves);

        return $this->moves[$key] ?? null;
    }

    public function map(callable $callback): array
    {
        return array_map($callback, $this->moves);
    }

    public function getIterator(): \Generator
    {
        yield from $this->moves;
    }

    public function count(): int
    {
        return sizeof($this->moves);
    }
}

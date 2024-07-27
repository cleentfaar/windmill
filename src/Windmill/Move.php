<?php

namespace App\Windmill;

class Move
{
    /**
     * @param Position[] $from
     * @param Position[] $to
     */
    public function __construct(
        public readonly array $from,
        public readonly array $to,
        public readonly ?string $comment = null
    ) {
    }

    public function fileDifference(): int
    {
        return abs($this->from[0]->file() - $this->to[0]->file());
    }

    public function rankDifference(): int
    {
        return abs($this->from[0]->rank() - $this->to[0]->rank());
    }

    public function staysOnFile(): bool
    {
        return $this->from[0]->file() == $this->to[0]->file();
    }
}

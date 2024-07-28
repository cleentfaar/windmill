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

    public function fileDifference(int $index = 0): int
    {
        return abs($this->from[$index]->file() - $this->to[$index]->file());
    }

    public function rankDifference(int $index = 0): int
    {
        return abs($this->from[$index]->rank() - $this->to[$index]->rank());
    }

    public function staysOnFile(int $index = 0): bool
    {
        return $this->from[$index]->file() == $this->to[$index]->file();
    }
}

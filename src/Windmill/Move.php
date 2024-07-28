<?php

namespace App\Windmill;

class Move
{
    public readonly PrimaryMove $primary;
    public readonly ?SecondaryMove $secondary;

    /**
     * @param Position[] $from
     * @param Position[] $to
     */
    public function __construct(
        public readonly array $from,
        public readonly array $to,
        public readonly ?string $comment = null
    ) {
        $this->primary = new PrimaryMove($from[0], $to[0]);
        $this->secondary = isset($from[1]) ? new SecondaryMove($from[1], $to[1] ?? null) : null;
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

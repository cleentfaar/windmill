<?php

namespace App\Windmill\Piece;

use App\Windmill\Color;

abstract class AbstractPiece
{
    final public function __construct(
        public readonly Color $color,
    ) {
    }

    public static function name(): string
    {
        $parts = explode('\\', static::class);

        return array_pop($parts);
    }

    public function isKing(): bool
    {
        return $this::class == King::class;
    }
}

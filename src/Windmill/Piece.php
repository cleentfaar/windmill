<?php

namespace App\Windmill;

class Piece
{
    final public function __construct(
        public readonly Color $color,
        public readonly PieceType $type,
    ) {
    }

    public static function name(): string
    {
        $parts = explode('\\', static::class);

        return array_pop($parts);
    }
}

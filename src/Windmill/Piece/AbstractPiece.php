<?php

namespace App\Windmill\Piece;

use App\Windmill\Color;
use Symfony\Component\Uid\Uuid;

abstract class AbstractPiece
{
    public readonly Color $color;
    public readonly Uuid $id;
    public readonly PieceHistory $history;

    final public function __construct(
        Color $color,
        Uuid $id = null,
        PieceHistory $history = new PieceHistory()
    ) {
        $this->color = $color;
        $this->id = $id ?: Uuid::v4();
        $this->history = $history;
    }

    public static function name(): string
    {
        $parts = explode('\\', static::class);

        return array_pop($parts);
    }
}

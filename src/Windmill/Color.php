<?php

namespace App\Windmill;

enum Color: int
{
    case WHITE = 1;
    case BLACK = 2;

    public static function oppositeOf(Color $color): Color
    {
        return $color == self::WHITE ? Color::BLACK : Color::WHITE;
    }

    public function name(): string
    {
        return ucfirst(strtolower($this->name));
    }
}

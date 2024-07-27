<?php

namespace App\Windmill;

enum Color: int
{
    case WHITE = 1;
    case BLACK = 2;

    public static function oppositeOf(Color $color): Color
    {
        return self::WHITE == $color ? Color::BLACK : Color::WHITE;
    }

    public static function fromName(string $name): Color
    {
        return Color::WHITE->name == $name ? Color::WHITE : Color::BLACK;
    }

    public function name(): string
    {
        return ucfirst(strtolower($this->name));
    }
}

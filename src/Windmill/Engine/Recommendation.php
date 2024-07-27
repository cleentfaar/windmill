<?php

namespace App\Windmill\Engine;

use App\Windmill\Move\AbstractMove;

class Recommendation
{
    public function __construct(public readonly AbstractMove $move, public readonly int $confidence = 100)
    {
    }
}

<?php

namespace App\Windmill\Engine;

use App\Windmill\Move\MultiMove;

class Recommendation
{
    public function __construct(public readonly MultiMove $move, public readonly int $confidence = 100)
    {
    }
}

<?php

namespace App\Windmill\Engine;

use App\Windmill\Move;

class Recommendation
{
    public function __construct(
        public readonly Move $move,
        public readonly int $confidence = 100
    ) {
    }
}

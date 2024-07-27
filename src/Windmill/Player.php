<?php

namespace App\Windmill;

use App\Windmill\Engine\RecommendationEngineInterface;

class Player implements PlayerInterface
{
    public function __construct(
        public readonly Color $color,
        public readonly string $name,
        public readonly RecommendationEngineInterface $engine,
    ) {
    }
}

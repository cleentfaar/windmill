<?php

namespace App\Windmill\Engine;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Game;

class Random implements RecommendationEngineInterface
{
    public function __construct(private readonly DelegatingCalculator $calculator)
    {
    }

    public function recommend(Game $game): Recommendation
    {
        $moves = $this->calculator->calculate($game)->all();
        $key = array_rand($moves, 1);

        return new Recommendation($moves[$key], rand(1, 100));
    }
}

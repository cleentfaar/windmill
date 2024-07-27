<?php

namespace App\Windmill\Engine;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Game;

class Random implements RecommendationEngineInterface
{
    public function __construct(private readonly DelegatingCalculator $calculator = new DelegatingCalculator())
    {
    }

    public function recommend(Game $game): Recommendation
    {
        $move = $this->calculator->calculate($game)->pickRandom();

        return new Recommendation($move, rand(1, 100));
    }
}

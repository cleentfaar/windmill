<?php

namespace App\Windmill\Engine;

use App\Windmill\Game;

interface RecommendationEngineInterface
{
    public function recommend(Game $game): Recommendation;
}

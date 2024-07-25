<?php

namespace App\Windmill\Engine;

use App\Windmill\Game;

abstract class AbstractEngine
{
    abstract public function recommend(Game $game): Recommendation;
}

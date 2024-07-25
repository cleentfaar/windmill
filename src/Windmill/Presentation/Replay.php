<?php

namespace App\Windmill\Presentation;

use App\Windmill\Game;
use App\Windmill\Move\MoveCollection;

class Replay
{
    public function __construct(
        public readonly Game $game,
        public readonly MoveCollection $moves,
    ) {
    }
}

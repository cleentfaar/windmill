<?php

namespace App\Windmill\Presentation;

use App\Windmill\Game;
use App\Windmill\MoveCollection;
use App\Windmill\State;

class Replay
{
    public function __construct(
        public readonly Game $game,
        public readonly MoveCollection $moves,
        public readonly State $state,
    ) {
    }
}

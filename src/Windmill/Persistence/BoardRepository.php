<?php

namespace App\Windmill\Persistence;

use App\Windmill\Board;
use App\Windmill\Game;
use Symfony\Component\Uid\Uuid;

interface BoardRepository
{
    public function find(Uuid $id): ?Board;
    public function save(Board $game): void;
}

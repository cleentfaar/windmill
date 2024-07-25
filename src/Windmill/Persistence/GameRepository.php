<?php

namespace App\Windmill\Persistence;

use App\Windmill\Game;
use Symfony\Component\Uid\Uuid;

interface GameRepository
{
	public function find(Uuid $id): ?Game;

	public function save(Game $game): void;
}

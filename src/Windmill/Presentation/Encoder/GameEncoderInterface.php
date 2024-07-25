<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Game;
use App\Windmill\PlayerInterface;

interface GameEncoderInterface
{
	public function encode(Game $game): string;

	public function decode(string $encodedGame, PlayerInterface $whitePlayer, PlayerInterface $blackPlayer): Game;
}

<?php

namespace App\Windmill;

class Player implements PlayerInterface
{
	public function __construct(
		public readonly Color $color,
		public readonly string $name,
		public readonly ?string $engine,
	) {
	}
}

<?php

namespace App\Windmill\Move;

use App\Windmill\Position;

class SimpleMove extends AbstractMove
{
	public function __construct(
		public readonly Position $from,
		public readonly Position $to
	) {
	}
}

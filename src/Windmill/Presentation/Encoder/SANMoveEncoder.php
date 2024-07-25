<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Calculation\DelegatingCalculator;

class SANMoveEncoder extends AlgebraicMoveEncoder
{
	public function __construct(
		private readonly PieceEncoderInterface $pieceEncoder = new SANPieceEncoder(),
		private readonly DelegatingCalculator $calculator = new DelegatingCalculator()
	) {
		parent::__construct($this->pieceEncoder, $this->calculator);
	}
}

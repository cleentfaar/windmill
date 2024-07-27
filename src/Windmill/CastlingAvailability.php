<?php

namespace App\Windmill;

class CastlingAvailability
{
    public function __construct(
        public bool $whiteCanCastleKingside = true,
        public bool $whiteCanCastleQueenside = true,
        public bool $blackCanCastleKingside = true,
        public bool $blackCanCastleQueenside = true,
    ) {
    }
}

<?php

namespace App\Windmill;

class PrimaryMove
{
    public function __construct(public readonly Position $from, public readonly Position $to)
    {
    }
}
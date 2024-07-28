<?php

namespace App\Windmill;

class SecondaryMove
{
    public function __construct(public readonly Position $from, public readonly ?Position $to)
    {
    }
}
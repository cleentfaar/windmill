<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;

interface BoardEncoderInterface
{
    public function encode(Board $board): string;
}

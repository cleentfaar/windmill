<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Presentation\Replay;

interface ReplayEncoderInterface
{
    public function encode(Replay $game): string;

    public function decode(string $game): Replay;
}

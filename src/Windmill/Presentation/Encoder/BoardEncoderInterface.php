<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use Symfony\Component\Uid\Uuid;

interface BoardEncoderInterface
{
    public function encode(Board $board): string;

    public function decode(string $encodedBoard, Uuid $boardId = null): Board;
}

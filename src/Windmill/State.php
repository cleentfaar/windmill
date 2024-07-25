<?php

namespace App\Windmill;

enum State: int
{
    case STARTED = 1;
    case FINISHED_WHITE_WINS = 101;
    case FINISHED_BLACK_WINS = 102;
    case FINISHED_DRAW = 103;
}

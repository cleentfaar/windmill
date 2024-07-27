<?php

namespace App\Windmill;

enum CheckState: int
{
    case NONE = 1;
    case CHECK = 2;
    case CHECKMATE = 3;
}

<?php

namespace App\Tests\Windmill\Piece;

use App\Tests\AbstractTestCase;
use App\Windmill\Piece\AbstractPiece;

class AbstractPieceTest extends AbstractTestCase
{
    public function testName()
    {
        $this->assertEquals('AbstractPiece', AbstractPiece::name());
    }
}

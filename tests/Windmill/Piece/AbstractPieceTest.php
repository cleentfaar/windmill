<?php

namespace App\Tests\Windmill\Piece;

use App\Tests\AbstractTestCase;
use App\Windmill\Piece;

class AbstractPieceTest extends AbstractTestCase
{
    public function testName()
    {
        $this->assertEquals('Piece', Piece::name());
    }
}

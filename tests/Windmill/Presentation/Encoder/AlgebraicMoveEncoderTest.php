<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Tests\AbstractTestCase;
use App\Windmill\Move;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;

class AlgebraicMoveEncoderTest extends AbstractTestCase
{
    public function testDecode()
    {
        $game = self::createGameFromFEN('4k3/3P4/8/8/8/8/8/4K3 b - - 0 1');
        $encoder = new AlgebraicMoveEncoder();
        $actualMove = $encoder->decode('Kxd7', $game);
        $expectedMove = new Move([Position::E8, Position::D7], [Position::D7, null]);

        $this->assertEquals($expectedMove, $actualMove);
    }

    public function testEncode()
    {
        $game = self::createGameFromFEN('4k3/3P4/8/8/8/8/8/4K3 b - - 0 1');
        $encoder = new AlgebraicMoveEncoder();
        $actualEncoding = $encoder->encode(new Move([Position::E8, Position::D7], [Position::D7, null]), $game);
        $this->assertEquals('Kxd7', $actualEncoding);
    }
}

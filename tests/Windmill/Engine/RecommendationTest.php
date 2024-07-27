<?php

namespace App\Tests\Windmill\Engine;

use App\Windmill\Engine\Recommendation;
use App\Windmill\Move;
use App\Windmill\Position;
use PHPUnit\Framework\TestCase;

class RecommendationTest extends TestCase
{
    public function testConstruct()
    {
        $r = new Recommendation(
            $move = new Move([Position::A2], [Position::A3]),
            $confidence = 100
        );

        $this->assertEquals($move, $r->move);
        $this->assertEquals($confidence, $r->confidence);
    }
}

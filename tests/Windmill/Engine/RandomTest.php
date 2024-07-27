<?php

namespace App\Tests\Windmill\Engine;

use App\Tests\AbstractTestCase;
use App\Windmill\Engine\Random;
use App\Windmill\Engine\Recommendation;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class RandomTest extends AbstractTestCase
{
    public function testRecommend()
    {
        $game = self::createGameFromFEN(FENGameEncoder::STANDARD_FEN);
        $engine = new Random();
        $recommendation = $engine->recommend($game);

        $this->assertInstanceOf(Recommendation::class, $recommendation);
    }
}

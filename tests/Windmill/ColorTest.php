<?php

namespace App\Tests\Windmill;

use App\Tests\AbstractTestCase;
use App\Windmill\Color;

class ColorTest extends AbstractTestCase
{
    public function testOppositeOf()
    {
        $this->assertEquals(Color::BLACK, Color::oppositeOf(Color::WHITE));
        $this->assertEquals(Color::WHITE, Color::oppositeOf(Color::BLACK));
    }

    public function testName()
    {
        $this->assertEquals('Black', Color::BLACK->name());
        $this->assertEquals('White', Color::WHITE->name());
    }
}

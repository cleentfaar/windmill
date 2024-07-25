<?php

namespace App\Tests\Windmill;

use App\Tests\AbstractTestCase;
use App\Windmill\Board;
use App\Windmill\BoardWalker;
use App\Windmill\Color;
use App\Windmill\Position;
use Symfony\Component\Uid\Uuid;

class BoardWalkerTest extends AbstractTestCase
{
	public function testMoveAroundBoard(
	): void {
		$board = new Board(Uuid::v4());
		$calculator = new BoardWalker(Position::E5, Color::WHITE, $board, true);

		$positions = $calculator->forward()->forward()->flush();
		$this->assertEquals([Position::E6, Position::E7], $positions);
		$positions = $calculator->backward()->flush();
		$this->assertEquals([Position::E6], $positions);
	}
}

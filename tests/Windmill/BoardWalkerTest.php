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
	public function testItCanMoveToLeftAndRightAdheringToPlayerOrientation(): void
	{
		$board = new Board(Uuid::v4());

		$calculator = new BoardWalker(Position::E5, Color::WHITE, $board);
		$this->assertEquals(Position::D5, $calculator->left()->current());

		$calculator = new BoardWalker(Position::E5, Color::BLACK, $board);
		$this->assertEquals(Position::F5, $calculator->left()->current());
	}

	public function testItCanMoveToAbsoluteLeftAndRightThusIgnoringPlayerOrientation(): void
	{
		$board = new Board(Uuid::v4());

		$calculator = new BoardWalker(Position::E5, Color::WHITE, $board);
		$this->assertEquals(Position::D5, $calculator->absoluteLeft()->current());

		$calculator = new BoardWalker(Position::E5, Color::BLACK, $board);
		$this->assertEquals(Position::D5, $calculator->absoluteLeft()->current());
	}

	public function testItReturnsPreviousPositionsOnFlushAndStartsOver(): void
	{
		$board = new Board(Uuid::v4());
		$calculator = new BoardWalker(Position::E5, Color::WHITE, $board);

		$positions = $calculator->forward()->forward()->flush();
		$this->assertEquals([Position::E6, Position::E7], $positions);
		$positions = $calculator->backward()->flush();
		$this->assertEquals([Position::E6], $positions);
	}
}

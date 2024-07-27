<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\Bishop;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Knight;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;
use App\Windmill\Piece\Rook;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\AlgebraicPieceEncoder;
use PHPUnit\Framework\TestCase;

class AlgebraicPieceEncoderTest extends TestCase
{
    /**
     * @dataProvider providePieceAndExpectedEncoding
     */
    public function testEncode(AbstractPiece $piece, Position $position, string $expectedEncoding)
    {
        $encoder = new AlgebraicPieceEncoder();
        $actualEncoding = $encoder->encode($piece, $position);

        $this->assertEquals(
            $expectedEncoding,
            $actualEncoding
        );
    }

    /**
     * @dataProvider provideEncodedAlgebraicPieceAndExpectedObject
     */
    public function testDecode(string $algebraicPiece, Color $color, AbstractPiece $expectedPiece)
    {
        $encoder = new AlgebraicPieceEncoder();
        $actualDecodedPiece = $encoder->decode($algebraicPiece, $color);

        $this->assertInstanceOf($expectedPiece::class, $actualDecodedPiece);
        $this->assertEquals(
            $expectedPiece->color,
            $actualDecodedPiece->color
        );
    }

    private function providePieceAndExpectedEncoding(): array
    {
        return [
            [new Pawn(Color::WHITE), Position::A2, 'a'],
            [new Pawn(Color::BLACK), Position::G7, 'g'],
            [new Bishop(Color::WHITE), Position::D4, 'B'],
            [new Bishop(Color::BLACK), Position::D4, 'B'],
            [new Knight(Color::WHITE), Position::D4, 'N'],
            [new Knight(Color::BLACK), Position::D4, 'N'],
            [new Rook(Color::WHITE), Position::D4, 'R'],
            [new Rook(Color::BLACK), Position::D4, 'R'],
            [new Queen(Color::WHITE), Position::D4, 'Q'],
            [new Queen(Color::BLACK), Position::D4, 'Q'],
            [new King(Color::WHITE), Position::D4, 'K'],
            [new King(Color::BLACK), Position::D4, 'K'],
        ];
    }

    private function provideEncodedAlgebraicPieceAndExpectedObject(): array
    {
        return [
            ['a', Color::WHITE, new Pawn(Color::WHITE)],
            ['g', Color::BLACK, new Pawn(Color::BLACK)],
            ['B', Color::WHITE, new Bishop(Color::WHITE)],
            ['B', Color::BLACK, new Bishop(Color::BLACK)],
            ['N', Color::WHITE, new Knight(Color::WHITE)],
            ['N', Color::BLACK, new Knight(Color::BLACK)],
            ['R', Color::WHITE, new Rook(Color::WHITE)],
            ['R', Color::BLACK, new Rook(Color::BLACK)],
            ['Q', Color::WHITE, new Queen(Color::WHITE)],
            ['Q', Color::BLACK, new Queen(Color::BLACK)],
            ['K', Color::WHITE, new King(Color::WHITE)],
            ['K', Color::BLACK, new King(Color::BLACK)],
        ];
    }
}

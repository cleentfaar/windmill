<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Windmill\Color;
use App\Windmill\Piece;
use App\Windmill\PieceType;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\AlgebraicPieceEncoder;
use PHPUnit\Framework\TestCase;

class AlgebraicPieceEncoderTest extends TestCase
{
    /**
     * @dataProvider providePieceAndExpectedEncoding
     */
    public function testEncode(Piece $piece, Position $position, string $expectedEncoding)
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
    public function testDecode(string $algebraicPiece, Color $color, Piece $expectedPiece)
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
            [new Piece(Color::WHITE, PieceType::PAWN), Position::A2, 'a'],
            [new Piece(Color::BLACK, PieceType::PAWN), Position::G7, 'g'],
            [new Piece(Color::WHITE, PieceType::BISHOP), Position::D4, 'B'],
            [new Piece(Color::BLACK, PieceType::BISHOP), Position::D4, 'B'],
            [new Piece(Color::WHITE, PieceType::KNIGHT), Position::D4, 'N'],
            [new Piece(Color::BLACK, PieceType::KNIGHT), Position::D4, 'N'],
            [new Piece(Color::WHITE, PieceType::ROOK), Position::D4, 'R'],
            [new Piece(Color::BLACK, PieceType::ROOK), Position::D4, 'R'],
            [new Piece(Color::WHITE, PieceType::QUEEN), Position::D4, 'Q'],
            [new Piece(Color::BLACK, PieceType::QUEEN), Position::D4, 'Q'],
            [new Piece(Color::WHITE, PieceType::KING), Position::D4, 'K'],
            [new Piece(Color::BLACK, PieceType::KING), Position::D4, 'K'],
        ];
    }

    private function provideEncodedAlgebraicPieceAndExpectedObject(): array
    {
        return [
            ['a', Color::WHITE, new Piece(Color::WHITE, PieceType::PAWN)],
            ['g', Color::BLACK, new Piece(Color::BLACK, PieceType::PAWN)],
            ['B', Color::WHITE, new Piece(Color::WHITE, PieceType::BISHOP)],
            ['B', Color::BLACK, new Piece(Color::BLACK, PieceType::BISHOP)],
            ['N', Color::WHITE, new Piece(Color::WHITE, PieceType::KNIGHT)],
            ['N', Color::BLACK, new Piece(Color::BLACK, PieceType::KNIGHT)],
            ['R', Color::WHITE, new Piece(Color::WHITE, PieceType::ROOK)],
            ['R', Color::BLACK, new Piece(Color::BLACK, PieceType::ROOK)],
            ['Q', Color::WHITE, new Piece(Color::WHITE, PieceType::QUEEN)],
            ['Q', Color::BLACK, new Piece(Color::BLACK, PieceType::QUEEN)],
            ['K', Color::WHITE, new Piece(Color::WHITE, PieceType::KING)],
            ['K', Color::BLACK, new Piece(Color::BLACK, PieceType::KING)],
        ];
    }
}

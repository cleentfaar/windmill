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

    /**
     * @dataProvider provideFenAndMoveWithExpectedEncoding
     */
    public function testEncode(
        string $fen,
        Move $move,
        string $expectedEncoding
    ) {
        $encoder = new AlgebraicMoveEncoder();

        $game = self::createGameFromFEN($fen);
        $actualEncoding = $encoder->encode($move, $game);
        $this->assertEquals($expectedEncoding, $actualEncoding);
    }

    private function provideFenAndMoveWithExpectedEncoding(): array
    {
        return [
            [
                '4k3/3P4/8/8/8/8/8/4K3 b - - 0 1',
                new Move([Position::E8, Position::D7], [Position::D7, null]),
                'Kxd7',
            ],
            [
                'r1bqkb1r/pp3ppp/2nppn2/1N6/2P1P3/8/PP3PPP/RNBQKB1R w KQkq - 1 7',
                new Move([Position::B1], [Position::C3]),
                'N1c3',
            ],
        ];
    }
}

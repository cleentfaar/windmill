<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Tests\AbstractTestCase;
use App\Windmill\Move;
use App\Windmill\Position;
use App\Windmill\Presentation\Encoder\FENGameEncoder;
use App\Windmill\Presentation\Encoder\VerboseMoveEncoder;

class VerboseMoveEncoderTest extends AbstractTestCase
{
    /**
     * @dataProvider provideMovesAndExpectedEncodings
     */
    public function testEncode(
        string $fen,
        Move $move,
        string $expectedEncoding
    ) {
        $game = self::createGameFromFEN($fen);
        $encoder = new VerboseMoveEncoder();
        $actualEncoding = $encoder->encode($move, $game);

        $this->assertEquals($expectedEncoding, $actualEncoding);
    }

    private function provideMovesAndExpectedEncodings(): array
    {
        return [
            'regular pawn move' => [
                FENGameEncoder::STANDARD_FEN,
                new Move([Position::A2], [Position::A4]),
                'Pawn to A4',
            ],
            'queen capture' => [
                '4k3/pppppppp/8/8/8/8/8/3QK3 w KQ - 0 1',
                new Move([Position::D1, Position::D7], [Position::D7, null]),
                'Queen takes Pawn on D7',
            ],
            'king castles queenside' => [
                '4k3/pppppppp/8/8/8/8/8/R3K3 w KQ - 0 1',
                new Move([Position::E1, Position::A1], [Position::C1, Position::D1]),
                'King castles queenside',
            ],
            'king castles kingside' => [
                '4k3/pppppppp/8/8/8/8/8/4K2R w KQ - 0 1',
                new Move([Position::E1, Position::H1], [Position::G1, Position::F1]),
                'King castles kingside',
            ],
        ];
    }
}

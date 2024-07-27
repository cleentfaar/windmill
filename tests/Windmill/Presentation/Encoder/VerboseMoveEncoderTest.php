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
            'king castles queenside' => [
                '4k3/pppppppp/8/8/8/8/8/R3K3 w KQ - 0 1',
                new Move([Position::E1, Position::A1], [Position::B1, Position::C1]),
                'King castles queenside',
            ],
            'king castles kingside' => [
                '4k3/pppppppp/8/8/8/8/8/R3K3 w KQ - 0 1',
                new Move([Position::E1, Position::H1], [Position::G1, Position::F1]),
                'King castles kingside',
            ],
        ];
    }
}

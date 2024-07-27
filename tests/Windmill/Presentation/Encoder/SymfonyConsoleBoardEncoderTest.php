<?php

namespace App\Tests\Windmill\Presentation\Encoder;

use App\Tests\AbstractTestCase;
use App\Windmill\Presentation\Encoder\FENGameEncoder;
use App\Windmill\Presentation\Encoder\SymfonyConsoleBoardEncoder;

class SymfonyConsoleBoardEncoderTest extends AbstractTestCase
{
    /**
     * @dataProvider provideFenAndExpectedAscii
     */
    public function testEncode(
        string $FEN,
        string $whiteFg,
        string $whiteBg,
        string $blackFg,
        string $blackBg,
        string $expectedOutput
    ): void {
        $game = self::createGameFromFEN($FEN);
        $encoder = new SymfonyConsoleBoardEncoder(
            true,
            true,
            ' ',
            $whiteFg,
            $whiteBg,
            $blackFg,
            $blackBg,
        );
        $actualOutput = $encoder->encode($game->board);

        $this->assertEquals($expectedOutput, $actualOutput);
    }

    public function provideFenAndExpectedAscii()
    {
        return [
            [
                FENGameEncoder::STANDARD_FEN,
                $whiteFg = 'abc',
                $whiteBg = 'def',
                $blackFg = 'ghi',
                $blackBg = 'jfk',
                <<<EOF

8 <fg=$blackFg;bg=$whiteBg> ♖ </><fg=$blackFg;bg=$blackBg> ♘ </><fg=$blackFg;bg=$whiteBg> ♗ </><fg=$blackFg;bg=$blackBg> ♕ </><fg=$blackFg;bg=$whiteBg> ♔ </><fg=$blackFg;bg=$blackBg> ♗ </><fg=$blackFg;bg=$whiteBg> ♘ </><fg=$blackFg;bg=$blackBg> ♖ </>
7 <fg=$blackFg;bg=$blackBg> ♙ </><fg=$blackFg;bg=$whiteBg> ♙ </><fg=$blackFg;bg=$blackBg> ♙ </><fg=$blackFg;bg=$whiteBg> ♙ </><fg=$blackFg;bg=$blackBg> ♙ </><fg=$blackFg;bg=$whiteBg> ♙ </><fg=$blackFg;bg=$blackBg> ♙ </><fg=$blackFg;bg=$whiteBg> ♙ </>
6 <fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </>
5 <fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </>
4 <fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </>
3 <fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </><fg=$blackFg;bg=$blackBg>   </><fg=$blackFg;bg=$whiteBg>   </>
2 <fg=$whiteFg;bg=$whiteBg> ♟︎ </><fg=$whiteFg;bg=$blackBg> ♟︎ </><fg=$whiteFg;bg=$whiteBg> ♟︎ </><fg=$whiteFg;bg=$blackBg> ♟︎ </><fg=$whiteFg;bg=$whiteBg> ♟︎ </><fg=$whiteFg;bg=$blackBg> ♟︎ </><fg=$whiteFg;bg=$whiteBg> ♟︎ </><fg=$whiteFg;bg=$blackBg> ♟︎ </>
1 <fg=$whiteFg;bg=$blackBg> ♜ </><fg=$whiteFg;bg=$whiteBg> ♞ </><fg=$whiteFg;bg=$blackBg> ♝ </><fg=$whiteFg;bg=$whiteBg> ♛ </><fg=$whiteFg;bg=$blackBg> ♚ </><fg=$whiteFg;bg=$whiteBg> ♝ </><fg=$whiteFg;bg=$blackBg> ♞ </><fg=$whiteFg;bg=$whiteBg> ♜ </>
   A  B  C  D  E  F  G  H 

EOF,
            ],
        ];
    }
}

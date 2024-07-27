<?php

namespace App\Tests\Windmill;

use App\Tests\AbstractTestCase;
use App\Windmill\Presentation\Encoder\FENGameEncoder;
use App\Windmill\Presentation\Encoder\SANMoveEncoder;

class EndToEndGameTest extends AbstractTestCase
{
    /**
     * @dataProvider gameStateProvider
     */
    public function testStateAfterEachMove(
        string $startingFEN,
        array ...$movesAndResultingStates
    ): void {
        $moveEncoder = new SANMoveEncoder();
        $gameEncoder = new FENGameEncoder();
        $game = self::createGameFromFEN($startingFEN);

        foreach ($movesAndResultingStates as list($move, $state)) {
            $game->move($moveEncoder->decode($move, $game));
            $this->assertEquals($state, $gameEncoder->encode($game), sprintf('Failed on %s', $move));
        }
    }

    public function gameStateProvider()
    {
        return [
            [
                FENGameEncoder::STANDARD_FEN,
                ['Nf3', 'rnbqkbnr/pppppppp/8/8/8/5N2/PPPPPPPP/RNBQKB1R b KQkq - 1 1'],
                ['Nf6', 'rnbqkb1r/pppppppp/5n2/8/8/5N2/PPPPPPPP/RNBQKB1R w KQkq - 2 2'],
                ['c4', 'rnbqkb1r/pppppppp/5n2/8/2P5/5N2/PP1PPPPP/RNBQKB1R b KQkq c4 0 2'],
                ['g6', 'rnbqkb1r/pppppp1p/5np1/8/2P5/5N2/PP1PPPPP/RNBQKB1R w KQkq c4 0 3'],
                ['Nc3', 'rnbqkb1r/pppppp1p/5np1/8/2P5/2N2N2/PP1PPPPP/R1BQKB1R b KQkq c4 1 3'],
                ['Bg7', 'rnbqk2r/ppppppbp/5np1/8/2P5/2N2N2/PP1PPPPP/R1BQKB1R w KQkq c4 2 4'],
                ['d4', 'rnbqk2r/ppppppbp/5np1/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R b KQkq d4 0 4'],
                ['0-0', 'rnbq1rk1/ppppppbp/5np1/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R w KQ d4 1 5'],
                ['Bf4', 'rnbq1rk1/ppppppbp/5np1/8/2PP1B2/2N2N2/PP2PPPP/R2QKB1R b KQ d4 2 5'],
                ['d5', 'rnbq1rk1/ppp1ppbp/5np1/3p4/2PP1B2/2N2N2/PP2PPPP/R2QKB1R w KQ d5 0 6'],
                ['Qb3', 'rnbq1rk1/ppp1ppbp/5np1/3p4/2PP1B2/1QN2N2/PP2PPPP/R3KB1R b KQ d5 1 6'],
                ['dxc4', 'rnbq1rk1/ppp1ppbp/5np1/8/2pP1B2/1QN2N2/PP2PPPP/R3KB1R w KQ d5 0 7'],
                ['Qxc4', 'rnbq1rk1/ppp1ppbp/5np1/8/2QP1B2/2N2N2/PP2PPPP/R3KB1R b KQ d5 0 7'],
                ['c6', 'rnbq1rk1/pp2ppbp/2p2np1/8/2QP1B2/2N2N2/PP2PPPP/R3KB1R w KQ d5 0 8'],
                ['e4', 'rnbq1rk1/pp2ppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R b KQ e4 0 8'],
                ['Nbd7', 'r1bq1rk1/pp1nppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R w KQ e4 1 9'],
                ['Rd1', 'r1bq1rk1/pp1nppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/3RKB1R b KQ e4 2 9'],
                ['Nb6', 'r1bq1rk1/pp2ppbp/1np2np1/8/2QPPB2/2N2N2/PP3PPP/3RKB1R w KQ e4 3 10'],
                ['Qc5', 'r1bq1rk1/pp2ppbp/1np2np1/2Q5/3PPB2/2N2N2/PP3PPP/3RKB1R b KQ e4 4 10'],
                ['Bg4', 'r2q1rk1/pp2ppbp/1np2np1/2Q5/3PPBb1/2N2N2/PP3PPP/3RKB1R w KQ e4 5 11'],
                ['Bg5', 'r2q1rk1/pp2ppbp/1np2np1/2Q3B1/3PP1b1/2N2N2/PP3PPP/3RKB1R b KQ e4 6 11'],
                ['Na4', 'r2q1rk1/pp2ppbp/2p2np1/2Q3B1/n2PP1b1/2N2N2/PP3PPP/3RKB1R w KQ e4 7 12'],
                ['Qa3', 'r2q1rk1/pp2ppbp/2p2np1/6B1/n2PP1b1/Q1N2N2/PP3PPP/3RKB1R b KQ e4 8 12'],
                ['Nxc3', 'r2q1rk1/pp2ppbp/2p2np1/6B1/3PP1b1/Q1n2N2/PP3PPP/3RKB1R w KQ e4 0 13'],
                ['bxc3', 'r2q1rk1/pp2ppbp/2p2np1/6B1/3PP1b1/Q1P2N2/P4PPP/3RKB1R b KQ e4 0 13'],
                ['Nxe4', 'r2q1rk1/pp2ppbp/2p3p1/6B1/3Pn1b1/Q1P2N2/P4PPP/3RKB1R w KQ e4 0 14'],
                ['Bxe7', 'r2q1rk1/pp2Bpbp/2p3p1/8/3Pn1b1/Q1P2N2/P4PPP/3RKB1R b KQ e4 0 14'],
                ['Qb6', 'r4rk1/pp2Bpbp/1qp3p1/8/3Pn1b1/Q1P2N2/P4PPP/3RKB1R w KQ e4 1 15'],
                ['Bc4', 'r4rk1/pp2Bpbp/1qp3p1/8/2BPn1b1/Q1P2N2/P4PPP/3RK2R b KQ e4 2 15'],
                ['Nxc3', 'r4rk1/pp2Bpbp/1qp3p1/8/2BP2b1/Q1n2N2/P4PPP/3RK2R w KQ e4 0 16'],
                ['Bc5', 'r4rk1/pp3pbp/1qp3p1/2B5/2BP2b1/Q1n2N2/P4PPP/3RK2R b KQ e4 1 16'],
                ['Rfe8+', 'r3r1k1/pp3pbp/1qp3p1/2B5/2BP2b1/Q1n2N2/P4PPP/3RK2R w KQ e4 2 17'],
                ['Kf1', 'r3r1k1/pp3pbp/1qp3p1/2B5/2BP2b1/Q1n2N2/P4PPP/3R1K1R b KQ e4 3 17'],
                ['Be6', 'r3r1k1/pp3pbp/1qp1b1p1/2B5/2BP4/Q1n2N2/P4PPP/3R1K1R w KQ e4 4 18'],
                ['Bxb6', 'r3r1k1/pp3pbp/1Bp1b1p1/8/2BP4/Q1n2N2/P4PPP/3R1K1R b KQ e4 0 18'],
                ['Bxc4+', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q1n2N2/P4PPP/3R1K1R w KQ e4 0 19'],
                ['Kg1', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q1n2N2/P4PPP/3R2KR b KQ e4 1 19'],
                ['Ne2+', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q4N2/P3nPPP/3R2KR w KQ e4 2 20'],
                ['Kf1', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q4N2/P3nPPP/3R1K1R b KQ e4 3 20'],
            ],
        ];
    }
}

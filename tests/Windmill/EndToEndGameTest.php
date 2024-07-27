<?php

namespace App\Tests\Windmill;

use App\Tests\AbstractTestCase;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class EndToEndGameTest extends AbstractTestCase
{
    /**
     * @dataProvider gameStateProvider
     */
    public function testStateAfterEachMove(
        string $startingFEN,
        array ...$movesAndResultingStates
    ): void {
        $moveEncoder = new AlgebraicMoveEncoder();
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
                ['Nxd4+', 'r3r1k1/pp3pbp/1Bp3p1/8/2bn4/Q4N2/P4PPP/3R1K1R w KQ e4 0 21'],
                ['Kg1', 'r3r1k1/pp3pbp/1Bp3p1/8/2bn4/Q4N2/P4PPP/3R2KR b KQ e4 1 21'],
                ['Ne2+', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q4N2/P3nPPP/3R2KR w KQ e4 2 22'],
                ['Kf1', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q4N2/P3nPPP/3R1K1R b KQ e4 3 22'],
                ['Nc3+', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q1n2N2/P4PPP/3R1K1R w KQ e4 4 23'],
                ['Kg1', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q1n2N2/P4PPP/3R2KR b KQ e4 5 23'],
                ['axb6', 'r3r1k1/1p3pbp/1pp3p1/8/2b5/Q1n2N2/P4PPP/3R2KR w KQ e4 0 24'],
                ['Qb4', 'r3r1k1/1p3pbp/1pp3p1/8/1Qb5/2n2N2/P4PPP/3R2KR b KQ e4 1 24'],
                ['Ra4', '4r1k1/1p3pbp/1pp3p1/8/rQb5/2n2N2/P4PPP/3R2KR w KQ e4 2 25'],
                ['Qxb6', '4r1k1/1p3pbp/1Qp3p1/8/r1b5/2n2N2/P4PPP/3R2KR b KQ e4 0 25'],
                ['Nxd1', '4r1k1/1p3pbp/1Qp3p1/8/r1b5/5N2/P4PPP/3n2KR w KQ e4 0 26'],
                ['h3', '4r1k1/1p3pbp/1Qp3p1/8/r1b5/5N1P/P4PP1/3n2KR b KQ e4 0 26'],
                ['Rxa2', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4PP1/3n2KR w KQ e4 0 27'],
                ['Kh2', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4PPK/3n3R b KQ e4 1 27'],
                ['Nxf2', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4nPK/7R w KQ e4 0 28'],
                ['Re1', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4nPK/4R3 b KQ e4 1 28'],
                ['Rxe1', '6k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4nPK/4r3 w KQ e4 0 29'],
                ['Qd8+', '3Q2k1/1p3pbp/2p3p1/8/2b5/5N1P/r4nPK/4r3 b KQ e4 1 29'],
                ['Bf8', '3Q1bk1/1p3p1p/2p3p1/8/2b5/5N1P/r4nPK/4r3 w KQ e4 2 30'],
                ['Nxe1', '3Q1bk1/1p3p1p/2p3p1/8/2b5/7P/r4nPK/4N3 b KQ e4 0 30'],
                ['Bd5', '3Q1bk1/1p3p1p/2p3p1/3b4/8/7P/r4nPK/4N3 w KQ e4 1 31'],
                ['Nf3', '3Q1bk1/1p3p1p/2p3p1/3b4/8/5N1P/r4nPK/8 b KQ e4 2 31'],
                ['Ne4', '3Q1bk1/1p3p1p/2p3p1/3b4/4n3/5N1P/r5PK/8 w KQ e4 3 32'],
                ['Qb8', '1Q3bk1/1p3p1p/2p3p1/3b4/4n3/5N1P/r5PK/8 b KQ e4 4 32'],
                ['b5', '1Q3bk1/5p1p/2p3p1/1p1b4/4n3/5N1P/r5PK/8 w KQ b5 0 33'],
                ['h4', '1Q3bk1/5p1p/2p3p1/1p1b4/4n2P/5N2/r5PK/8 b KQ b5 0 33'],
                ['h5', '1Q3bk1/5p2/2p3p1/1p1b3p/4n2P/5N2/r5PK/8 w KQ h5 0 34'],
                ['Ne5', '1Q3bk1/5p2/2p3p1/1p1bN2p/4n2P/8/r5PK/8 b KQ h5 1 34'],
                ['Kg7', '1Q3b2/5pk1/2p3p1/1p1bN2p/4n2P/8/r5PK/8 w KQ h5 2 35'],
                ['Kg1', '1Q3b2/5pk1/2p3p1/1p1bN2p/4n2P/8/r5P1/6K1 b KQ h5 3 35'],
                ['Bc5+', '1Q6/5pk1/2p3p1/1pbbN2p/4n2P/8/r5P1/6K1 w KQ h5 4 36'],
                ['Kf1', '1Q6/5pk1/2p3p1/1pbbN2p/4n2P/8/r5P1/5K2 b KQ h5 5 36'],
                ['Ng3+', '1Q6/5pk1/2p3p1/1pbbN2p/7P/6n1/r5P1/5K2 w KQ h5 6 37'],
                ['Ke1', '1Q6/5pk1/2p3p1/1pbbN2p/7P/6n1/r5P1/4K3 b KQ h5 7 37'],
                ['Bb4+', '1Q6/5pk1/2p3p1/1p1bN2p/1b5P/6n1/r5P1/4K3 w KQ h5 8 38'],
                ['Kd1', '1Q6/5pk1/2p3p1/1p1bN2p/1b5P/6n1/r5P1/3K4 b KQ h5 9 38'],
                ['Bb3+', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b4n1/r5P1/3K4 w KQ h5 10 39'],
                ['Kc1', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b4n1/r5P1/2K5 b KQ h5 11 39'],
                ['Ne2+', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b6/r3n1P1/2K5 w KQ h5 12 40'],
                ['Kb1', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b6/r3n1P1/1K6 b KQ h5 13 40'],
                ['Nc3+', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1bn5/r5P1/1K6 w KQ h5 14 41'],
                ['Kc1', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1bn5/r5P1/2K5 b KQ h5 15 41'],
            ],
        ];
    }
}

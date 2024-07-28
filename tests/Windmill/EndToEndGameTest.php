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
            try {
                $game->move($moveEncoder->decode($move, $game));
            } catch (\Exception $e) {
                throw $e;
            }
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
                ['g6', 'rnbqkb1r/pppppp1p/5np1/8/2P5/5N2/PP1PPPPP/RNBQKB1R w KQkq - 0 3'],
                ['Nc3', 'rnbqkb1r/pppppp1p/5np1/8/2P5/2N2N2/PP1PPPPP/R1BQKB1R b KQkq - 1 3'],
                ['Bg7', 'rnbqk2r/ppppppbp/5np1/8/2P5/2N2N2/PP1PPPPP/R1BQKB1R w KQkq - 2 4'],
                ['d4', 'rnbqk2r/ppppppbp/5np1/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R b KQkq d4 0 4'],
                ['0-0', 'rnbq1rk1/ppppppbp/5np1/8/2PP4/2N2N2/PP2PPPP/R1BQKB1R w KQ - 1 5'],
                ['Bf4', 'rnbq1rk1/ppppppbp/5np1/8/2PP1B2/2N2N2/PP2PPPP/R2QKB1R b KQ - 2 5'],
                ['d5', 'rnbq1rk1/ppp1ppbp/5np1/3p4/2PP1B2/2N2N2/PP2PPPP/R2QKB1R w KQ d5 0 6'],
                ['Qb3', 'rnbq1rk1/ppp1ppbp/5np1/3p4/2PP1B2/1QN2N2/PP2PPPP/R3KB1R b KQ - 1 6'],
                ['dxc4', 'rnbq1rk1/ppp1ppbp/5np1/8/2pP1B2/1QN2N2/PP2PPPP/R3KB1R w KQ - 0 7'],
                ['Qxc4', 'rnbq1rk1/ppp1ppbp/5np1/8/2QP1B2/2N2N2/PP2PPPP/R3KB1R b KQ - 0 7'],
                ['c6', 'rnbq1rk1/pp2ppbp/2p2np1/8/2QP1B2/2N2N2/PP2PPPP/R3KB1R w KQ - 0 8'],
                ['e4', 'rnbq1rk1/pp2ppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R b KQ e4 0 8'],
                ['Nbd7', 'r1bq1rk1/pp1nppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/R3KB1R w KQ - 1 9'],
                ['Rd1', 'r1bq1rk1/pp1nppbp/2p2np1/8/2QPPB2/2N2N2/PP3PPP/3RKB1R b - - 2 9'],
                ['Nb6', 'r1bq1rk1/pp2ppbp/1np2np1/8/2QPPB2/2N2N2/PP3PPP/3RKB1R w - - 3 10'],
                ['Qc5', 'r1bq1rk1/pp2ppbp/1np2np1/2Q5/3PPB2/2N2N2/PP3PPP/3RKB1R b - - 4 10'],
                ['Bg4', 'r2q1rk1/pp2ppbp/1np2np1/2Q5/3PPBb1/2N2N2/PP3PPP/3RKB1R w - - 5 11'],
                ['Bg5', 'r2q1rk1/pp2ppbp/1np2np1/2Q3B1/3PP1b1/2N2N2/PP3PPP/3RKB1R b - - 6 11'],
                ['Na4', 'r2q1rk1/pp2ppbp/2p2np1/2Q3B1/n2PP1b1/2N2N2/PP3PPP/3RKB1R w - - 7 12'],
                ['Qa3', 'r2q1rk1/pp2ppbp/2p2np1/6B1/n2PP1b1/Q1N2N2/PP3PPP/3RKB1R b - - 8 12'],
                ['Nxc3', 'r2q1rk1/pp2ppbp/2p2np1/6B1/3PP1b1/Q1n2N2/PP3PPP/3RKB1R w - - 0 13'],
                ['bxc3', 'r2q1rk1/pp2ppbp/2p2np1/6B1/3PP1b1/Q1P2N2/P4PPP/3RKB1R b - - 0 13'],
                ['Nxe4', 'r2q1rk1/pp2ppbp/2p3p1/6B1/3Pn1b1/Q1P2N2/P4PPP/3RKB1R w - - 0 14'],
                ['Bxe7', 'r2q1rk1/pp2Bpbp/2p3p1/8/3Pn1b1/Q1P2N2/P4PPP/3RKB1R b - - 0 14'],
                ['Qb6', 'r4rk1/pp2Bpbp/1qp3p1/8/3Pn1b1/Q1P2N2/P4PPP/3RKB1R w - - 1 15'],
                ['Bc4', 'r4rk1/pp2Bpbp/1qp3p1/8/2BPn1b1/Q1P2N2/P4PPP/3RK2R b - - 2 15'],
                ['Nxc3', 'r4rk1/pp2Bpbp/1qp3p1/8/2BP2b1/Q1n2N2/P4PPP/3RK2R w - - 0 16'],
                ['Bc5', 'r4rk1/pp3pbp/1qp3p1/2B5/2BP2b1/Q1n2N2/P4PPP/3RK2R b - - 1 16'],
                ['Rfe8+', 'r3r1k1/pp3pbp/1qp3p1/2B5/2BP2b1/Q1n2N2/P4PPP/3RK2R w - - 2 17'],
                ['Kf1', 'r3r1k1/pp3pbp/1qp3p1/2B5/2BP2b1/Q1n2N2/P4PPP/3R1K1R b - - 3 17'],
                ['Be6', 'r3r1k1/pp3pbp/1qp1b1p1/2B5/2BP4/Q1n2N2/P4PPP/3R1K1R w - - 4 18'],
                ['Bxb6', 'r3r1k1/pp3pbp/1Bp1b1p1/8/2BP4/Q1n2N2/P4PPP/3R1K1R b - - 0 18'],
                ['Bxc4+', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q1n2N2/P4PPP/3R1K1R w - - 0 19'],
                ['Kg1', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q1n2N2/P4PPP/3R2KR b - - 1 19'],
                ['Ne2+', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q4N2/P3nPPP/3R2KR w - - 2 20'],
                ['Kf1', 'r3r1k1/pp3pbp/1Bp3p1/8/2bP4/Q4N2/P3nPPP/3R1K1R b - - 3 20'],
                ['Nxd4+', 'r3r1k1/pp3pbp/1Bp3p1/8/2bn4/Q4N2/P4PPP/3R1K1R w - - 0 21'],
                ['Kg1', 'r3r1k1/pp3pbp/1Bp3p1/8/2bn4/Q4N2/P4PPP/3R2KR b - - 1 21'],
                ['Ne2+', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q4N2/P3nPPP/3R2KR w - - 2 22'],
                ['Kf1', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q4N2/P3nPPP/3R1K1R b - - 3 22'],
                ['Nc3+', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q1n2N2/P4PPP/3R1K1R w - - 4 23'],
                ['Kg1', 'r3r1k1/pp3pbp/1Bp3p1/8/2b5/Q1n2N2/P4PPP/3R2KR b - - 5 23'],
                ['axb6', 'r3r1k1/1p3pbp/1pp3p1/8/2b5/Q1n2N2/P4PPP/3R2KR w - - 0 24'],
                ['Qb4', 'r3r1k1/1p3pbp/1pp3p1/8/1Qb5/2n2N2/P4PPP/3R2KR b - - 1 24'],
                ['Ra4', '4r1k1/1p3pbp/1pp3p1/8/rQb5/2n2N2/P4PPP/3R2KR w - - 2 25'],
                ['Qxb6', '4r1k1/1p3pbp/1Qp3p1/8/r1b5/2n2N2/P4PPP/3R2KR b - - 0 25'],
                ['Nxd1', '4r1k1/1p3pbp/1Qp3p1/8/r1b5/5N2/P4PPP/3n2KR w - - 0 26'],
                ['h3', '4r1k1/1p3pbp/1Qp3p1/8/r1b5/5N1P/P4PP1/3n2KR b - - 0 26'],
                ['Rxa2', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4PP1/3n2KR w - - 0 27'],
                ['Kh2', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4PPK/3n3R b - - 1 27'],
                ['Nxf2', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4nPK/7R w - - 0 28'],
                ['Re1', '4r1k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4nPK/4R3 b - - 1 28'],
                ['Rxe1', '6k1/1p3pbp/1Qp3p1/8/2b5/5N1P/r4nPK/4r3 w - - 0 29'],
                ['Qd8+', '3Q2k1/1p3pbp/2p3p1/8/2b5/5N1P/r4nPK/4r3 b - - 1 29'],
                ['Bf8', '3Q1bk1/1p3p1p/2p3p1/8/2b5/5N1P/r4nPK/4r3 w - - 2 30'],
                ['Nxe1', '3Q1bk1/1p3p1p/2p3p1/8/2b5/7P/r4nPK/4N3 b - - 0 30'],
                ['Bd5', '3Q1bk1/1p3p1p/2p3p1/3b4/8/7P/r4nPK/4N3 w - - 1 31'],
                ['Nf3', '3Q1bk1/1p3p1p/2p3p1/3b4/8/5N1P/r4nPK/8 b - - 2 31'],
                ['Ne4', '3Q1bk1/1p3p1p/2p3p1/3b4/4n3/5N1P/r5PK/8 w - - 3 32'],
                ['Qb8', '1Q3bk1/1p3p1p/2p3p1/3b4/4n3/5N1P/r5PK/8 b - - 4 32'],
                ['b5', '1Q3bk1/5p1p/2p3p1/1p1b4/4n3/5N1P/r5PK/8 w - b5 0 33'],
                ['h4', '1Q3bk1/5p1p/2p3p1/1p1b4/4n2P/5N2/r5PK/8 b - - 0 33'],
                ['h5', '1Q3bk1/5p2/2p3p1/1p1b3p/4n2P/5N2/r5PK/8 w - h5 0 34'],
                ['Ne5', '1Q3bk1/5p2/2p3p1/1p1bN2p/4n2P/8/r5PK/8 b - - 1 34'],
                ['Kg7', '1Q3b2/5pk1/2p3p1/1p1bN2p/4n2P/8/r5PK/8 w - - 2 35'],
                ['Kg1', '1Q3b2/5pk1/2p3p1/1p1bN2p/4n2P/8/r5P1/6K1 b - - 3 35'],
                ['Bc5+', '1Q6/5pk1/2p3p1/1pbbN2p/4n2P/8/r5P1/6K1 w - - 4 36'],
                ['Kf1', '1Q6/5pk1/2p3p1/1pbbN2p/4n2P/8/r5P1/5K2 b - - 5 36'],
                ['Ng3+', '1Q6/5pk1/2p3p1/1pbbN2p/7P/6n1/r5P1/5K2 w - - 6 37'],
                ['Ke1', '1Q6/5pk1/2p3p1/1pbbN2p/7P/6n1/r5P1/4K3 b - - 7 37'],
                ['Bb4+', '1Q6/5pk1/2p3p1/1p1bN2p/1b5P/6n1/r5P1/4K3 w - - 8 38'],
                ['Kd1', '1Q6/5pk1/2p3p1/1p1bN2p/1b5P/6n1/r5P1/3K4 b - - 9 38'],
                ['Bb3+', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b4n1/r5P1/3K4 w - - 10 39'],
                ['Kc1', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b4n1/r5P1/2K5 b - - 11 39'],
                ['Ne2+', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b6/r3n1P1/2K5 w - - 12 40'],
                ['Kb1', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1b6/r3n1P1/1K6 b - - 13 40'],
                ['Nc3+', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1bn5/r5P1/1K6 w - - 14 41'],
                ['Kc1', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1bn5/r5P1/2K5 b - - 15 41'],
                ['Rc2#', '1Q6/5pk1/2p3p1/1p2N2p/1b5P/1bn5/2r3P1/2K5 w - - 16 42'],
            ],
            [
                FENGameEncoder::STANDARD_FEN,
                ['e4', 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e4 0 1'],
                ['d6', 'rnbqkbnr/ppp1pppp/3p4/8/4P3/8/PPPP1PPP/RNBQKBNR w KQkq - 0 2'],
                ['d4', 'rnbqkbnr/ppp1pppp/3p4/8/3PP3/8/PPP2PPP/RNBQKBNR b KQkq d4 0 2'],
                ['Nf6', 'rnbqkb1r/ppp1pppp/3p1n2/8/3PP3/8/PPP2PPP/RNBQKBNR w KQkq - 1 3'],
                ['Nc3', 'rnbqkb1r/ppp1pppp/3p1n2/8/3PP3/2N5/PPP2PPP/R1BQKBNR b KQkq - 2 3'],
                ['g6', 'rnbqkb1r/ppp1pp1p/3p1np1/8/3PP3/2N5/PPP2PPP/R1BQKBNR w KQkq - 0 4'],
                ['Be3', 'rnbqkb1r/ppp1pp1p/3p1np1/8/3PP3/2N1B3/PPP2PPP/R2QKBNR b KQkq - 1 4'],
                ['Bg7', 'rnbqk2r/ppp1ppbp/3p1np1/8/3PP3/2N1B3/PPP2PPP/R2QKBNR w KQkq - 2 5'],
                ['Qd2', 'rnbqk2r/ppp1ppbp/3p1np1/8/3PP3/2N1B3/PPPQ1PPP/R3KBNR b KQkq - 3 5'],
                ['c6', 'rnbqk2r/pp2ppbp/2pp1np1/8/3PP3/2N1B3/PPPQ1PPP/R3KBNR w KQkq - 0 6'],
                ['f3', 'rnbqk2r/pp2ppbp/2pp1np1/8/3PP3/2N1BP2/PPPQ2PP/R3KBNR b KQkq - 0 6'],
                ['b5', 'rnbqk2r/p3ppbp/2pp1np1/1p6/3PP3/2N1BP2/PPPQ2PP/R3KBNR w KQkq b5 0 7'],
                ['Nge2', 'rnbqk2r/p3ppbp/2pp1np1/1p6/3PP3/2N1BP2/PPPQN1PP/R3KB1R b KQkq - 1 7'],
                ['Nbd7', 'r1bqk2r/p2nppbp/2pp1np1/1p6/3PP3/2N1BP2/PPPQN1PP/R3KB1R w KQkq - 2 8'],
                ['Bh6', 'r1bqk2r/p2nppbp/2pp1npB/1p6/3PP3/2N2P2/PPPQN1PP/R3KB1R b KQkq - 3 8'],
                ['Bxh6', 'r1bqk2r/p2npp1p/2pp1npb/1p6/3PP3/2N2P2/PPPQN1PP/R3KB1R w KQkq - 0 9'],
                ['Qxh6', 'r1bqk2r/p2npp1p/2pp1npQ/1p6/3PP3/2N2P2/PPP1N1PP/R3KB1R b KQkq - 0 9'],
                ['Bb7', 'r2qk2r/pb1npp1p/2pp1npQ/1p6/3PP3/2N2P2/PPP1N1PP/R3KB1R w KQkq - 1 10'],
                ['a3', 'r2qk2r/pb1npp1p/2pp1npQ/1p6/3PP3/P1N2P2/1PP1N1PP/R3KB1R b KQkq - 0 10'],
                ['e5', 'r2qk2r/pb1n1p1p/2pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/R3KB1R w KQkq e5 0 11'],
                ['0-0-0', 'r2qk2r/pb1n1p1p/2pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/2KR1B1R b kq - 1 11'],
                ['Qe7', 'r3k2r/pb1nqp1p/2pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/2KR1B1R w kq - 2 12'],
                ['Kb1', 'r3k2r/pb1nqp1p/2pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/1K1R1B1R b kq - 3 12'],
                ['a6', 'r3k2r/1b1nqp1p/p1pp1npQ/1p2p3/3PP3/P1N2P2/1PP1N1PP/1K1R1B1R w kq - 0 13'],
                ['Nc1', 'r3k2r/1b1nqp1p/p1pp1npQ/1p2p3/3PP3/P1N2P2/1PP3PP/1KNR1B1R b kq - 1 13'],
                ['0-0-0', '2kr3r/1b1nqp1p/p1pp1npQ/1p2p3/3PP3/P1N2P2/1PP3PP/1KNR1B1R w - - 2 14'],
                ['Nb3', '2kr3r/1b1nqp1p/p1pp1npQ/1p2p3/3PP3/PNN2P2/1PP3PP/1K1R1B1R b - - 3 14'],
                ['exd4', '2kr3r/1b1nqp1p/p1pp1npQ/1p6/3pP3/PNN2P2/1PP3PP/1K1R1B1R w - - 0 15'],
                ['Rxd4', '2kr3r/1b1nqp1p/p1pp1npQ/1p6/3RP3/PNN2P2/1PP3PP/1K3B1R b - - 0 15'],
                ['c5', '2kr3r/1b1nqp1p/p2p1npQ/1pp5/3RP3/PNN2P2/1PP3PP/1K3B1R w - - 0 16'],
                ['Rd1', '2kr3r/1b1nqp1p/p2p1npQ/1pp5/4P3/PNN2P2/1PP3PP/1K1R1B1R b - - 1 16'],
                ['Nb6', '2kr3r/1b2qp1p/pn1p1npQ/1pp5/4P3/PNN2P2/1PP3PP/1K1R1B1R w - - 2 17'],
                ['g3', '2kr3r/1b2qp1p/pn1p1npQ/1pp5/4P3/PNN2PP1/1PP4P/1K1R1B1R b - - 0 17'],
                ['Kb8', '1k1r3r/1b2qp1p/pn1p1npQ/1pp5/4P3/PNN2PP1/1PP4P/1K1R1B1R w - - 1 18'],
                ['Na5', '1k1r3r/1b2qp1p/pn1p1npQ/Npp5/4P3/P1N2PP1/1PP4P/1K1R1B1R b - - 2 18'],
                ['Ba8', 'bk1r3r/4qp1p/pn1p1npQ/Npp5/4P3/P1N2PP1/1PP4P/1K1R1B1R w - - 3 19'],
                ['Bh3', 'bk1r3r/4qp1p/pn1p1npQ/Npp5/4P3/P1N2PPB/1PP4P/1K1R3R b - - 4 19'],
                ['d5', 'bk1r3r/4qp1p/pn3npQ/Nppp4/4P3/P1N2PPB/1PP4P/1K1R3R w - - 0 20'],
                ['Qf4+', 'bk1r3r/4qp1p/pn3np1/Nppp4/4PQ2/P1N2PPB/1PP4P/1K1R3R b - - 1 20'],
                ['Ka7', 'b2r3r/k3qp1p/pn3np1/Nppp4/4PQ2/P1N2PPB/1PP4P/1K1R3R w - - 2 21'],
                ['Rhe1', 'b2r3r/k3qp1p/pn3np1/Nppp4/4PQ2/P1N2PPB/1PP4P/1K1RR3 b - - 3 21'],
                ['d4', 'b2r3r/k3qp1p/pn3np1/Npp5/3pPQ2/P1N2PPB/1PP4P/1K1RR3 w - - 0 22'],
                ['Nd5', 'b2r3r/k3qp1p/pn3np1/NppN4/3pPQ2/P4PPB/1PP4P/1K1RR3 b - - 1 22'],
                ['Nbxd5', 'b2r3r/k3qp1p/p4np1/Nppn4/3pPQ2/P4PPB/1PP4P/1K1RR3 w - - 0 23'],
                ['exd5', 'b2r3r/k3qp1p/p4np1/NppP4/3p1Q2/P4PPB/1PP4P/1K1RR3 b - - 0 23'],
                ['Qd6', 'b2r3r/k4p1p/p2q1np1/NppP4/3p1Q2/P4PPB/1PP4P/1K1RR3 w - - 1 24'],
                ['Rxd4', 'b2r3r/k4p1p/p2q1np1/NppP4/3R1Q2/P4PPB/1PP4P/1K2R3 b - - 0 24'],
                ['cxd4', 'b2r3r/k4p1p/p2q1np1/Np1P4/3p1Q2/P4PPB/1PP4P/1K2R3 w - - 0 25'],
                ['Re7+', 'b2r3r/k3Rp1p/p2q1np1/Np1P4/3p1Q2/P4PPB/1PP4P/1K6 b - - 1 25'],
                ['Kb6', 'b2r3r/4Rp1p/pk1q1np1/Np1P4/3p1Q2/P4PPB/1PP4P/1K6 w - - 2 26'],
                ['Qxd4+', 'b2r3r/4Rp1p/pk1q1np1/Np1P4/3Q4/P4PPB/1PP4P/1K6 b - - 0 26'],
                ['Kxa5', 'b2r3r/4Rp1p/p2q1np1/kp1P4/3Q4/P4PPB/1PP4P/1K6 w - - 0 27'],
                ['b4+', 'b2r3r/4Rp1p/p2q1np1/kp1P4/1P1Q4/P4PPB/2P4P/1K6 b - b4 0 27'],
                ['Ka4', 'b2r3r/4Rp1p/p2q1np1/1p1P4/kP1Q4/P4PPB/2P4P/1K6 w - - 1 28'],
                ['Qc3', 'b2r3r/4Rp1p/p2q1np1/1p1P4/kP6/P1Q2PPB/2P4P/1K6 b - - 2 28'],
                ['Qxd5', 'b2r3r/4Rp1p/p4np1/1p1q4/kP6/P1Q2PPB/2P4P/1K6 w - - 0 29'],
                ['Ra7', 'b2r3r/R4p1p/p4np1/1p1q4/kP6/P1Q2PPB/2P4P/1K6 b - - 1 29'],
                ['Bb7', '3r3r/Rb3p1p/p4np1/1p1q4/kP6/P1Q2PPB/2P4P/1K6 w - - 2 30'],
                ['Rxb7', '3r3r/1R3p1p/p4np1/1p1q4/kP6/P1Q2PPB/2P4P/1K6 b - - 0 30'],
                ['Qc4', '3r3r/1R3p1p/p4np1/1p6/kPq5/P1Q2PPB/2P4P/1K6 w - - 1 31'],
                ['Qxf6', '3r3r/1R3p1p/p4Qp1/1p6/kPq5/P4PPB/2P4P/1K6 b - - 0 31'],
                ['Kxa3', '3r3r/1R3p1p/p4Qp1/1p6/1Pq5/k4PPB/2P4P/1K6 w - - 0 32'],
                ['Qxa6+', '3r3r/1R3p1p/Q5p1/1p6/1Pq5/k4PPB/2P4P/1K6 b - - 0 32'],
                ['Kxb4', '3r3r/1R3p1p/Q5p1/1p6/1kq5/5PPB/2P4P/1K6 w - - 0 33'],
                ['c3+', '3r3r/1R3p1p/Q5p1/1p6/1kq5/2P2PPB/7P/1K6 b - - 0 33'],
                ['Kxc3', '3r3r/1R3p1p/Q5p1/1p6/2q5/2k2PPB/7P/1K6 w - - 0 34'],
                ['Qa1+', '3r3r/1R3p1p/6p1/1p6/2q5/2k2PPB/7P/QK6 b - - 1 34'],
                ['Kd2', '3r3r/1R3p1p/6p1/1p6/2q5/5PPB/3k3P/QK6 w - - 2 35'],
                ['Qb2+', '3r3r/1R3p1p/6p1/1p6/2q5/5PPB/1Q1k3P/1K6 b - - 3 35'],
                ['Kd1', '3r3r/1R3p1p/6p1/1p6/2q5/5PPB/1Q5P/1K1k4 w - - 4 36'],
                ['Bf1', '3r3r/1R3p1p/6p1/1p6/2q5/5PP1/1Q5P/1K1k1B2 b - - 5 36'],
                ['Rd2', '7r/1R3p1p/6p1/1p6/2q5/5PP1/1Q1r3P/1K1k1B2 w - - 6 37'],
                ['Rd7', '7r/3R1p1p/6p1/1p6/2q5/5PP1/1Q1r3P/1K1k1B2 b - - 7 37'],
                ['Rxd7', '7r/3r1p1p/6p1/1p6/2q5/5PP1/1Q5P/1K1k1B2 w - - 0 38'],
                ['Bxc4', '7r/3r1p1p/6p1/1p6/2B5/5PP1/1Q5P/1K1k4 b - - 0 38'],
                ['bxc4', '7r/3r1p1p/6p1/8/2p5/5PP1/1Q5P/1K1k4 w - - 0 39'],
                ['Qxh8', '7Q/3r1p1p/6p1/8/2p5/5PP1/7P/1K1k4 b - - 0 39'],
                ['Rd3', '7Q/5p1p/6p1/8/2p5/3r1PP1/7P/1K1k4 w - - 1 40'],
                ['Qa8', 'Q7/5p1p/6p1/8/2p5/3r1PP1/7P/1K1k4 b - - 2 40'],
                ['c3', 'Q7/5p1p/6p1/8/8/2pr1PP1/7P/1K1k4 w - - 0 41'],
                ['Qa4+', '8/5p1p/6p1/8/Q7/2pr1PP1/7P/1K1k4 b - - 1 41'],
                ['Ke1', '8/5p1p/6p1/8/Q7/2pr1PP1/7P/1K2k3 w - - 2 42'],
                ['f4', '8/5p1p/6p1/8/Q4P2/2pr2P1/7P/1K2k3 b - - 0 42'],
                ['f5', '8/7p/6p1/5p2/Q4P2/2pr2P1/7P/1K2k3 w - f5 0 43'],
                ['Kc1', '8/7p/6p1/5p2/Q4P2/2pr2P1/7P/2K1k3 b - - 1 43'],
                ['Rd2', '8/7p/6p1/5p2/Q4P2/2p3P1/3r3P/2K1k3 w - - 2 44'],
                ['Qa7', '8/Q6p/6p1/5p2/5P2/2p3P1/3r3P/2K1k3 b - - 3 44'],
            ],
        ];
    }
}

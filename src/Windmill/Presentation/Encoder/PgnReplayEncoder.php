<?php

namespace App\Windmill\Presentation\Encoder;

use AmyBoyd\PgnParser\Game as PgnGame;
use AmyBoyd\PgnParser\PgnParser;
use App\Windmill\Game;
use App\Windmill\GameFactory;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Presentation\Replay;

class PgnReplayEncoder implements ReplayEncoderInterface
{
    public function __construct(private readonly MoveEncoderInterface $moveEncoder = new SANMoveEncoder())
    {
    }

    public function encode(Replay $game): string
    {
    }

    public function decode(string $game, bool $returnUpToFailure = false): Replay
    {
        $parser = new PgnParser($game);
        $parsedGame = $parser->getGame(0);
        $game = $this->createGameFromMetadata($parsedGame);
        $moves = new MoveCollection();

        foreach ($parsedGame->getMovesArray() as $pgnMove) {
            try {
                $move = $this->moveEncoder->decode($pgnMove, $game);
                $game->move($move);

                $moves->add($move);
            } catch (\Exception $e) {
                if ($returnUpToFailure) {
                    break;
                }

                $fen = new FENGameEncoder();
                dump($fen->encode($game));
                throw $e;
            }
        }

        return new Replay($this->createGameFromMetadata($parsedGame), $moves);
    }

    private function createGameFromMetadata(PgnGame $parsedGame): Game
    {
        return GameFactory::standard(
            $parsedGame->getWhite(),
            null,
            $parsedGame->getBlack(),
            null,
        );
    }
}

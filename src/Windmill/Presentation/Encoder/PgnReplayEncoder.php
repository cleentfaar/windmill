<?php

namespace App\Windmill\Presentation\Encoder;

use AmyBoyd\PgnParser\Game as PgnGame;
use AmyBoyd\PgnParser\PgnParser;
use App\Windmill\Engine\Random;
use App\Windmill\Game;
use App\Windmill\GameFactory;
use App\Windmill\MoveCollection;
use App\Windmill\Presentation\Replay;
use App\Windmill\State;

class PgnReplayEncoder implements ReplayEncoderInterface
{
    public function __construct(private readonly MoveEncoderInterface $moveEncoder = new AlgebraicMoveEncoder())
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

        return new Replay(
            $this->createGameFromMetadata($parsedGame),
            $moves,
            $this->createStateFromMetadata($parsedGame),
            $parsedGame->getEvent(),
            $parsedGame->getSite(),
        );
    }

    private function createGameFromMetadata(PgnGame $parsedGame): Game
    {
        return GameFactory::standard(
            $parsedGame->getWhite(),
            new Random(),
            $parsedGame->getBlack(),
            new Random(),
        );
    }

    private function createStateFromMetadata(PgnGame $parsedGame): State
    {
        return match ($parsedGame->getResult()) {
            '1-0' => State::FINISHED_WHITE_WINS,
            '1-1' => State::FINISHED_DRAW,
            '0-1' => State::FINISHED_BLACK_WINS,
            default => throw new \Exception(),
        };
    }
}

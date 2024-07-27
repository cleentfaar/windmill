<?php

namespace App\Windmill;

use App\Windmill\Engine\RecommendationEngineInterface;
use App\Windmill\Presentation\Encoder\FENGameEncoder;

class GameFactory
{
    public static function standard(
        string $whitePlayerName,
        RecommendationEngineInterface $whitePlayerEngine,
        string $blackPlayerName,
        RecommendationEngineInterface $blackPlayerEngine,
    ): Game {
        $whitePlayer = new Player(Color::WHITE, $whitePlayerName, $whitePlayerEngine);
        $blackPlayer = new Player(Color::BLACK, $blackPlayerName, $blackPlayerEngine);

        return self::standardWithPlayerObjects($whitePlayer, $blackPlayer);
    }

    public static function standardWithPlayerObjects(
        PlayerInterface $whitePlayer,
        PlayerInterface $blackPlayer,
    ): Game {
        return self::createFromFEN(
            $whitePlayer,
            $blackPlayer,
            FENGameEncoder::STANDARD_FEN
        );
    }

    public static function createFromFEN(
        PlayerInterface $whitePlayer,
        PlayerInterface $blackPlayer,
        string $fen,
    ): Game {
        return (new FENGameEncoder())->decode(
            $fen,
            $whitePlayer,
            $blackPlayer
        );
    }
}

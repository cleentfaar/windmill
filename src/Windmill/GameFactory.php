<?php

namespace App\Windmill;

use App\Windmill\Presentation\Encoder\FENGameEncoder;

class GameFactory
{
    public static function standard(
        string $whitePlayerName,
        ?string $whitePlayerEngine,
        string $blackPlayerName,
        ?string $blackPlayerEngine,
    ): Game {

        $whitePlayer = new Player(Color::WHITE, $whitePlayerName, $whitePlayerEngine);
        $blackPlayer = new Player(Color::BLACK, $blackPlayerName, $blackPlayerEngine);

        return self::standardWithPlayerObjects($whitePlayer, $blackPlayer);
    }

    public static function standardWithPlayerObjects(
        PlayerInterface $whitePlayer,
        PlayerInterface $blackPlayer,
    ): Game {
        return (new FENGameEncoder())->decode(
            FENGameEncoder::STANDARD_FEN,
            $whitePlayer,
            $blackPlayer
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

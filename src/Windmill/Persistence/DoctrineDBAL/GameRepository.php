<?php

namespace App\Windmill\Persistence\DoctrineDBAL;

use App\Windmill\Game;
use App\Windmill\Persistence\BoardRepository as BoardRepositoryInterface;
use App\Windmill\Persistence\GameRepository as GameRepositoryInterface;
use App\Windmill\Player;
use App\Windmill\State;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;

class GameRepository implements GameRepositoryInterface
{
    private const TYPES = [
        'white_player' => Types::JSON,
        'black_player' => Types::JSON,
        'started_on' => Types::DATETIME_IMMUTABLE,
        'ended_on' => Types::DATETIME_IMMUTABLE,
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly BoardRepositoryInterface $boardRepository
    ) {
    }

    public function find(Uuid $id): ?Game
    {
        $game = $this->connection->fetchAssociative(<<<END
SELECT
    *
FROM games
WHERE id = :id
END, ['id' => $id], self::TYPES);

        if ($game !== false) {
            $board = $this->boardRepository->find($game['board_id']);
            dump($game);
            // TODO find out why decoding is not automatic
            $wp = (array) json_decode($game['white_player']);
            $bp = (array) json_decode($game['black_player']);
            return new Game(
                Uuid::fromString($game['id']),
                new Player($wp['name'], $wp['engine']),
                new Player($bp['name'], $bp['engine']),
                State::from($game['state']),
                $board,
                DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game['started_on']),
                $game['ended_on']? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $game['ended_on']) : null,
            );
        }

        return null;
    }

    public function save(Game $game): void
    {
        $this->boardRepository->save($game->board);

        if ($this->find($game->id)) {
            $this->connection->update(
                'games',
                [
                    'white_player' => ['name' => $game->whitePlayer->getName(), 'engine' => $game->whitePlayer->getEngine()],
                    'black_player' => ['name' => $game->blackPlayer->getName(), 'engine' => $game->blackPlayer->getEngine()],
                    'state' => $game->state->value,
                    'ended_on' => $game->endedOn,
                ],
                ['id' => $game->id],
                self::TYPES
            );

            return;
        }

        $this->connection->insert(
            'games',
            [
                'id' => $game->id,
                'white_player' => ['name' => $game->whitePlayer->getName(), 'engine' => $game->whitePlayer->getEngine()],
                'black_player' => ['name' => $game->blackPlayer->getName(), 'engine' => $game->blackPlayer->getEngine()],
                'board_id' => $game->board->id,
                'state' => $game->state->value,
                'started_on' => $game->startedOn,
                'ended_on' => $game->endedOn,
            ],
            self::TYPES
        );
    }
}

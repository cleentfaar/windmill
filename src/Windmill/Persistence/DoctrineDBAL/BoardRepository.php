<?php

namespace App\Windmill\Persistence\DoctrineDBAL;

use App\Windmill\Board;
use App\Windmill\Persistence\BoardRepository as BoardRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;

class BoardRepository implements BoardRepositoryInterface
{
    private const TYPES = [
        'id' => Types::GUID,
        'squares' => Types::JSON,
    ];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function find(Uuid $id): ?Board
    {
        $board = $this->connection->fetchAssociative(<<<END
SELECT
    *
FROM boards
WHERE id = :id
END, ['id' => $id], self::TYPES);

        if (false !== $board) {
            dump($board);
            // TODO find out why decoding is not automatic
            $wp = (array) json_decode($board['squares']);

            return new Board(
                Uuid::fromString($board['id']),
                $wp,
            );
        }

        return null;
    }

    public function save(Board $board): void
    {
        if ($this->find($board->id)) {
            $this->connection->update(
                'boards',
                [
                    'squares' => $board->squares(),
                ],
                ['id' => $board->id],
                self::TYPES
            );

            return;
        }

        $this->connection->insert(
            'boards',
            [
                'id' => $board->id,
                'squares' => $board->squares(),
            ],
            self::TYPES
        );
    }
}

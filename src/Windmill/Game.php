<?php

namespace App\Windmill;

use App\Windmill\Piece\King;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Rook;
use Symfony\Component\Uid\Uuid;

class Game
{
    private ?Move $lastMove = null;

    public function __construct(
        public readonly Uuid $id,
        public readonly State $state,
        public readonly Board $board,
        public readonly PlayerInterface $whitePlayer,
        public readonly PlayerInterface $blackPlayer,
        private Color $colorToMove,
        public readonly CastlingAvailability $castlingAvailability,
        private ?Position $enPassantTargetSquare = null,
        private int $halfMoveClock = 0,
        private int $fullMoveClock = 1,
        public readonly ?\DateTimeImmutable $startedOn = null,
        public readonly ?\DateTimeImmutable $endedOn = null
    ) {
    }

    public function move(Move $move, bool $virtual = false)
    {
        if (!$virtual) {
            $this->updateStatesBeforeExecutingMove($move);
            $this->board->move($move);
            $this->colorToMove = Color::WHITE == $this->colorToMove ? Color::BLACK : Color::WHITE;
            $this->lastMove = $move;
        } else {
            $this->board->move($move);
        }
    }

    public function isFinished(): bool
    {
        return in_array(
            $this->state,
            [State::FINISHED_WHITE_WINS, State::FINISHED_BLACK_WINS, State::FINISHED_DRAW]
        );
    }

    public function currentColor(): Color
    {
        if ($this->isFinished()) {
            throw new \Exception('Game has finished, there is no current color to move');
        }

        return $this->colorToMove;
    }

    public function currentPlayer(): PlayerInterface
    {
        if ($this->isFinished()) {
            throw new \Exception('Game has finished, there is no current player to move');
        }

        return Color::WHITE == $this->colorToMove ? $this->whitePlayer : $this->blackPlayer;
    }

    private function updateStatesBeforeExecutingMove(Move $move): void
    {
        $halfMoveReset = false;

        if (
            Pawn::class == $this->board->pieceOn($move->from[0])::class
            && 2 == $move->rankDifference()
            && $move->staysOnFile()
        ) {
            $this->enPassantTargetSquare = $move->to[0];
        }

        if (Pawn::class == $this->board->pieceOn($move->from[0])::class) {
            // pawn is moving
            $halfMoveReset = true;
        }

        foreach ($move->from as $from) {
            $fromPiece = $this->board->pieceOn($from);
            if ($fromPiece && in_array($fromPiece::class, [King::class, Rook::class])) {
                // castle
                if (Color::WHITE == $this->colorToMove) {
                    $this->castlingAvailability->whiteCanCastleQueenside = false;
                    $this->castlingAvailability->whiteCanCastleKingside = false;
                } else {
                    $this->castlingAvailability->blackCanCastleQueenside = false;
                    $this->castlingAvailability->blackCanCastleKingside = false;
                }
            }
        }

        foreach ($move->to as $x => $to) {
            $fromPiece = $this->board->pieceOn($move->from[$x]);
            if (null == $to && $fromPiece && $fromPiece->color != $this->colorToMove) {
                // capture
                $halfMoveReset = true;
            }
        }

        if ($halfMoveReset) {
            $this->halfMoveClock = 0;
        } else {
            ++$this->halfMoveClock;
        }

        if (Color::BLACK == $this->colorToMove) {
            ++$this->fullMoveClock;
        }
    }

    public function halfMoveClock(): int
    {
        return $this->halfMoveClock;
    }

    public function fullMoveClock(): int
    {
        return $this->fullMoveClock;
    }

    public function enPassantTargetSquare(): ?Position
    {
        return $this->enPassantTargetSquare;
    }

    public function __clone(): void
    {
        $this->id = clone $this->id;
        $this->board = clone $this->board;
        $this->castlingAvailability = clone $this->castlingAvailability;
        $this->blackPlayer = clone $this->blackPlayer;
        $this->whitePlayer = clone $this->whitePlayer;
    }
}

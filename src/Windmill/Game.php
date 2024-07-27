<?php

namespace App\Windmill;

use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Pawn;
use Symfony\Component\Uid\Uuid;

class Game
{
    private ?AbstractMove $lastMove = null;

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

    public function move(AbstractMove $move, bool $virtual = false)
    {
        $this->updateStatesBeforeExecutingMove($move);
        $this->board->move($move);

        if (!$virtual) {
            $this->colorToMove = Color::WHITE == $this->colorToMove ? Color::BLACK : Color::WHITE;
            $this->lastMove = $move;
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

    private function updateStatesBeforeExecutingMove(AbstractMove $move): void
    {
        $halfMoveReset = false;

        switch ($move::class) {
            case SimpleMove::class:
                if (
                    2 == abs($move->from->rank() - $move->to->rank())
                    && $move->from->file() == $move->to->file()
                    && Pawn::class == $this->board->pieceOn($move->from)::class
                ) {
                    $this->enPassantTargetSquare = $move->to;
                }
                if (Pawn::class == $this->board->pieceOn($move->from)::class) {
                    // pawn is moving
                    $halfMoveReset = true;
                }
                break;
            case MultiMove::class:
                foreach ($move->from as $x => $from) {
                    if (King::class == $this->board->pieceOn($from)::class && abs($move->from[$x]->file() - $move->to[$x]->file()) > 1) {
                        // castle
                        if (Color::WHITE == $this->currentColor()) {
                            $this->castlingAvailability->whiteCanCastleQueenside = false;
                            $this->castlingAvailability->whiteCanCastleKingside = false;
                        } else {
                            $this->castlingAvailability->blackCanCastleQueenside = false;
                            $this->castlingAvailability->blackCanCastleKingside = false;
                        }
                    }
                }

                foreach ($move->to as $x => $to) {
                    if (null == $to && $this->board->pieceOn($move->from[$x])->color != $this->currentColor()) {
                        // capture
                        $halfMoveReset = true;
                    }
                }

                break;
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

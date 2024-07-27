<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\CheckState;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\AbstractPiece;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Pawn;
use App\Windmill\Position;

class AlgebraicMoveEncoder implements MoveEncoderInterface
{
    public function __construct(
        private readonly PieceEncoderInterface $pieceEncoder = new SANPieceEncoder(),
        private readonly DelegatingCalculator $calculator = new DelegatingCalculator()
    ) {
    }

    public function encode(AbstractMove $move, Game $game): string
    {
        switch ($move::class) {
            case SimpleMove::class:
//                $to = $move->to;
//                $movingPiece = $game->board->pieceOn($move->from);
//
//                if (!$movingPiece) {
//                    throw new \Exception(sprintf('There is no piece to move from that position: %s', $move->from->name));
//                }
//
//                $firstChar = $this->encodeMovingPieceChar($move->from, $move->to, $game->board);
//
//                $uniqueFile = $this->encodeUniqueFile(
//                    $movingPiece,
//                    $move,
//                    $game
//                );
//
//                $checksOrCheckmates = $this->encodeChecksOrCheckmatesOpponent($move, $game);
//
//                return sprintf(
//                    '%s%s%s%s%s',
//                    $firstChar,
//                    $uniqueFile,
//                    $to->fileLetter(),
//                    $to->rank(),
//                    $checksOrCheckmates
//                );
            case MultiMove::class:
                $moveFrom = $move::class == MultiMove::class ? $move->from[0] : $move->from;
                $moveTo = $move::class == MultiMove::class ? $move->to[0] : $move->to;
                $movingPiece = $game->board->pieceOn($moveFrom);

                if (King::class == $movingPiece::class && abs($moveFrom->file() - $moveTo->file()) > 1) {
                    // castling
                    $jumpSize = abs($moveFrom->file() - $moveTo->file());

                    if ($jumpSize > 3) {
                        return '0-0-0';
                    } else {
                        return '0-0';
                    }
                }

                $isCapture = ($move::class == MultiMove::class) ? (sizeof($move->to) > 1 && sizeof(array_filter($move->to)) == 1) : $game->board->pieceOn($move->to);
                $captureablePiecePosition = $isCapture ? ($move->from[1] == $move->to[0] ? $move->to[0] : $move->from[1]) : $move->to;
                $firstChar = $this->encodeMovingPieceChar($moveFrom, $captureablePiecePosition, $game->board);
                $uniqueFile = $this->encodeUniqueFile($movingPiece, $move, $game);
                $checksOrCheckmates = $this->encodeChecksOrCheckmatesOpponent($move, $game);

                return sprintf(
                    '%s%s%s%s%d%s',
                    $firstChar,
                    $uniqueFile,
                    $isCapture ? 'x': '',
                    $moveTo->fileLetter(),
                    $moveTo->rank(),
                    $checksOrCheckmates
                );
            default:
                throw new \Exception(sprintf("Moves of type '%s' can not be encoded", $move::class));
        }
    }

    public function decode(mixed $algebraic, Game $game): AbstractMove
    {
        $algebraic = str_replace('O', '0', $algebraic);
        $possibleMoves = [];

        foreach ($this->calculator->calculate($game)->all() as $move) {
            $encoded = $this->encode($move, $game);

            if ($encoded == $algebraic) {
                $possibleMoves[$encoded] = $move;
            }
        }

        if (1 != sizeof($possibleMoves)) {
            throw new \Exception(sprintf("Expected exactly one possible move that results into '%s', got %d%s", $algebraic, sizeof($possibleMoves), sizeof($possibleMoves) > 0 ? sprintf(' (%s)', implode(',', array_keys($possibleMoves))) : ''));
        }

        return array_shift($possibleMoves);
    }

    private function encodeUniqueFile(AbstractPiece $movingPiece, AbstractMove $move, Game $game): string
    {
        if ($this->calculator->calculcateMultiplePiecesWithDestination(
            $movingPiece::class,
            SimpleMove::class == $move::class ? $move->to : $move->to[0], $game
        )) {
            return (SimpleMove::class == $move::class ? $move->from : $move->from[0])->fileLetter();
        }

        return '';
    }

    private function encodeChecksOrCheckmatesOpponent(AbstractMove $move, Game $game): string
    {
        return match ($this->calculator->calculcateCheckState($move, $game)) {
            CheckState::CHECK => '+',
            CheckState::CHECKMATE => '#',
            default => '',
        };
    }

    public function encodePiece(AbstractPiece $movingPiece, SimpleMove $move): string
    {
        if (Pawn::class != $movingPiece::class) {
            return $this->pieceEncoder->encode($movingPiece, $move->from);
        }

        return '';
    }

    private function encodeMovingPieceChar(Position $from, Position $captureablePiecePosition, Board $board): string
    {
        $piece = $board->pieceOn($from);

        if (Pawn::class == $piece::class) {
            if ($board->pieceOn($captureablePiecePosition)) {
                return $from->fileLetter();
            }

            return '';
        }

        return $this->pieceEncoder->encode($piece, $from);
    }
}

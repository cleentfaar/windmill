<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\CheckState;
use App\Windmill\Game;
use App\Windmill\Move;
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

    public function encode(Move $move, Game $game): string
    {
        $moveFrom = $move->from[0];
        $moveTo = $move->to[0];
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

        $isCapture = sizeof($move->to) > 1 && 1 == sizeof(array_filter($move->to));
        $captureablePiecePosition = isset($move->from[1]) ? ($move->from[1] == $move->to[0] ? $move->to[0] : $move->from[1]) : $move->to[0];
        $firstChar = $this->encodeMovingPieceChar($moveFrom, $captureablePiecePosition, $game->board);
        $uniqueFile = $this->encodeUniqueFile($movingPiece, $move, $game);
        $checksOrCheckmates = $this->encodeChecksOrCheckmatesOpponent($move, $game);

        return join('', [
            $firstChar,
            $uniqueFile,
            $isCapture ? 'x' : '',
            $moveTo->fileLetter(),
            $moveTo->rank(),
            $checksOrCheckmates,
        ]);
    }

    public function decode(mixed $algebraic, Game $game): Move
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

    private function encodeUniqueFile(AbstractPiece $movingPiece, Move $move, Game $game): string
    {
        if ($this->calculator->calculcateMultiplePiecesWithDestination(
            $movingPiece::class,
            $move->to[0],
            $game
        )) {
            return $move->from[0]->fileLetter();
        }

        return '';
    }

    private function encodeChecksOrCheckmatesOpponent(Move $move, Game $game): string
    {
        return match ($this->calculator->calculcateCheckState($move, $game)) {
            CheckState::CHECK => '+',
            CheckState::CHECKMATE => '#',
            default => '',
        };
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

<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\CheckState;
use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Rook;

class AlgebraicMoveEncoder implements MoveEncoderInterface
{
    public function __construct(
        private readonly AlgebraicPieceEncoder $pieceEncoder = new AlgebraicPieceEncoder(),
        private readonly DelegatingCalculator $calculator = new DelegatingCalculator()
    ) {
    }

    public function encode(Move $move, Game $game): string
    {
        $movingPiece = $game->board->pieceOn($move->primary->from);

        if ($movingPiece->isKing()) {
            $secondaryPiece = $move->secondary && $move->secondary->to ? $game->board->pieceOn($move->secondary->from) : null;

            if (is_object($secondaryPiece) && $secondaryPiece::class == Rook::class && $secondaryPiece->color == $movingPiece->color) {
                // castling
                if ($move->fileDifference(1) > 2) {
                    return '0-0-0';
                } elseif (2 == $move->fileDifference(1)) {
                    return '0-0';
                }
            }
        }

        $isCapture = $move->secondary && $move->secondary->to == null;
        $firstChar = $this->encodeMovingPieceChar($move, $game->board);
        $uniqueFile = $this->encodeUniqueFileOrRank($move, $game);
        $checksOrCheckmates = $this->encodeCheckState($this->calculator->calculateCheckState($move, $game));

        return join('', [
            $firstChar,
            $uniqueFile,
            $isCapture ? 'x' : '',
            $move->primary->to->square(),
            $checksOrCheckmates,
        ]);
    }

    public function decode(mixed $algebraic, Game $game): Move
    {
        $algebraic = str_replace('O', '0', $algebraic);
        $possibleMoves = [];
        $availableMoves = $this->calculator->calculate($game);

        foreach ($availableMoves as $move) {
            $encoded = $this->encode($move, $game);

            if ($encoded == $algebraic) {
                $possibleMoves[$encoded] = $move;
            }
        }

        if (1 != sizeof($possibleMoves)) {
            dump((new AsciiBoardEncoder(true, true, ' '))->encode($game->board));
            throw new \Exception(sprintf("Expected exactly one possible move that results into '%s', got %d%s", $algebraic, sizeof($possibleMoves), sizeof($possibleMoves) > 0 ? sprintf(' (%s)', implode(',', array_keys($possibleMoves))) : ''));
        }

        return array_shift($possibleMoves);
    }

    private function encodeUniqueFileOrRank(Move $move, Game $game): string
    {
        $movesWithSameDestination = $this->calculator->calculcatePiecesOfTypeWithSameToButDifferentFrom(
            $move,
            $game
        );

        if (sizeof($movesWithSameDestination) == 0) {
            return '';
        }

        $movesWithSameFile = $movesWithSameDestination->fromFile($move->primary->from->file());

        if (sizeof($movesWithSameFile) == 0) {
            $movingPiece = $game->board->pieceOn($move->primary->from);

            if ($movingPiece::class != Pawn::class) {
                return $move->primary->from->fileLetter();
            } else {
                return '';
            }
        }

        return $move->primary->from->rank();
    }

    private function encodeCheckState(CheckState $checkState): string
    {
        return match ($checkState) {
            CheckState::CHECK => '+',
            CheckState::CHECKMATE => '#',
            default => '',
        };
    }

    private function encodeMovingPieceChar(Move $move, Board $board): string
    {
        $captureablePiecePosition = isset($move->secondary->from) ? ($move->secondary->from == $move->primary->to ? $move->primary->to : $move->secondary->from) : $move->primary->to;
        $piece = $board->pieceOn($move->primary->from);

        if (Pawn::class == $piece::class) {
            if ($board->pieceOn($captureablePiecePosition)) {
                return $move->primary->from->fileLetter();
            }

            return '';
        }

        return $this->pieceEncoder->encode($piece, $move->primary->from);
    }
}

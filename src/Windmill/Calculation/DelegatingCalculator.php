<?php

namespace App\Windmill\Calculation;

use App\Windmill\CheckState;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\MoveCollection;
use App\Windmill\Piece\King;
use App\Windmill\PieceType;
use App\Windmill\Position;

class DelegatingCalculator
{
    /**
     * @var AbstractPieceCalculator[]
     */
    private array $calculators;

    public function __construct(array $calculators = [])
    {
        $this->calculators = $calculators ?: [
            PieceType::PAWN->value => new PawnCalculator(),
            PieceType::BISHOP->value => new BishopCalculator(),
            PieceType::KNIGHT->value => new KnightCalculator(),
            PieceType::ROOK->value => new RookCalculator(),
            PieceType::QUEEN->value => new QueenCalculator(),
            PieceType::KING->value => new KingCalculator(),
        ];
    }

    public function calculate(Game $game): MoveCollection
    {
        $currentColor = $game->currentColor();
        $moveCollection = new MoveCollection();

        foreach ($game->board->squares() as $position => $piece) {
            if ($piece && $piece->color == $currentColor) {
                $this->calculators[$piece->type->value]->calculate(
                    $game,
                    Position::from($position),
                    $piece->color,
                    $moveCollection
                );
            }
        }

        return $moveCollection;
    }

    public function calculateCheckState(Move $move, Game $game): CheckState
    {
        $movingPiece = $game->board->pieceOn($move->primary->from);
        $clone = clone $game;
        $clone->move($move, true);
        $oppositeKingPosition = $clone->board->kingPosition(Color::oppositeOf($movingPiece->color));
        $moveCollection = $this->calculate($clone);
        $movesAttackingKing = $moveCollection->to($oppositeKingPosition);

        if (0 == sizeof($movesAttackingKing)) {
            return CheckState::NONE;
        }

        $clone = clone $game;
        $clone->move($move); // now king is checked, we need to see if he can escape

        $canEscape = true;

        foreach ($this->calculate($clone) as $moveToTryDefendTheKing) {
            $cloneForDefendingKing = clone $clone;
            $cloneForDefendingKing->move($moveToTryDefendTheKing);

            $movesThayMayStillBeAttackingKing = $this->calculate($cloneForDefendingKing);
            $currentKingSquare = $cloneForDefendingKing->board->kingPosition(Color::oppositeOf($movingPiece->color)); // needs to be recalculated as one of the moves may involve moving the king
            $movesThatAreStillAttackingKing = $movesThayMayStillBeAttackingKing->to($currentKingSquare);

            if (0 == sizeof($movesThatAreStillAttackingKing)) {
                // at least one way to escape
                return CheckState::CHECK;
            } else {
                $canEscape = false;
            }
        }

        return $canEscape ? CheckState::CHECK : CheckState::CHECKMATE;
    }

    public function calculcatePiecesOfTypeWithSameToButDifferentFrom(Move $move, Game $game): MoveCollection
    {
        $piece = $game->board->pieceOn($move->primary->from);
        $moves = $this->calculate($game)->to($move->primary->to);
        $eligible = [];

        foreach ($moves as $m) {
            foreach ($m->to as $x => $t) {
                $fromPiece = $game->board->pieceOn($m->from[$x]);
                if ($t == $move->primary->to && $fromPiece && $fromPiece->type == $piece->type && $m->from[$x] !== $move->primary->from) {
                    $eligible[] = $m;
                }
            }
        }

        return new MoveCollection($eligible);
    }
}

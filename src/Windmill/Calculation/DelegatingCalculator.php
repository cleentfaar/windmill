<?php

namespace App\Windmill\Calculation;

use App\Windmill\CheckState;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\MoveCollection;
use App\Windmill\Piece\Bishop;
use App\Windmill\Piece\King;
use App\Windmill\Piece\Knight;
use App\Windmill\Piece\Pawn;
use App\Windmill\Piece\Queen;
use App\Windmill\Piece\Rook;
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
            Pawn::class => new PawnCalculator(),
            Bishop::class => new BishopCalculator(),
            Knight::class => new KnightCalculator(),
            Rook::class => new RookCalculator(),
            Queen::class => new QueenCalculator(),
            King::class => new KingCalculator(),
        ];
    }

    public function calculate(Game $game): MoveCollection
    {
        $currentColor = $game->currentColor();
        $moveCollection = new MoveCollection();

        foreach ($game->board->squares() as $position => $piece) {
            if ($piece && $piece->color == $currentColor) {
                $this->calculators[$piece::class]->calculate(
                    $game,
                    Position::from($position),
                    $piece->color,
                    $moveCollection
                );
            }
        }

        return $moveCollection;
    }

    public function calculcateCheckState(Move $move, Game $game): CheckState
    {
        $moveFrom = $move->from[0];
        $movingPiece = $game->board->pieceOn($moveFrom);
        $clone = clone $game;
        $clone->move($move, true);
        $squaresWithOppositeKing = $clone->board->squaresWithPiece(King::class, Color::oppositeOf($movingPiece->color));

        if (1 != sizeof($squaresWithOppositeKing)) {
            throw new \Exception(sprintf('Expected a single square to be occupied by the king, got %d', sizeof($squaresWithOppositeKing)));
        }

        $moveCollection = $this->calculate($clone);
        $movesAttackingKing = $moveCollection->to($squaresWithOppositeKing[0])->all();

        if (0 == sizeof($movesAttackingKing)) {
            return CheckState::NONE;
        }

        $clone = clone $game;
        $clone->move($move);

        foreach ($movesAttackingKing as $m) {
            return CheckState::CHECK; // stop here until checkmate logic is fixed
            //            $movesByKing = $this->calculate($game)->from($squaresWithOppositeKing[0])->all();
            //
            //            if (0 == sizeof($movesByKing)) {
            //                // king has nowhere to go, checkmate
            //                return CheckState::CHECKMATE;
            //            }
            //
            //            foreach ($movesByKing as $moveByKing) {
            //                $g = clone $clone;
            //                $g->move($moveByKing);
            //                $newKingDestination = $moveByKing->to[0];
            //                $movesStillAttackingKing = $this->calculateWithDestination($newKingDestination, $g)->all();
            //
            //                if (sizeof($movesStillAttackingKing) > 0) {
            //                    return CheckState::CHECKMATE;
            //                }
            //            }
            //
            //            return CheckState::CHECK;
        }

        return CheckState::NONE;
    }

    public function calculcateMultiplePiecesWithDestination(string $pieceClass, Position $destination, Game $game): bool
    {
        $moves = $this->calculate($game)->to($destination)->all();
        $eligible = [];

        foreach ($moves as $m) {
            foreach ($m->to as $x => $t) {
                if ($t == $destination && $game->board->pieceOn($m->from[$x])::class == $pieceClass) {
                    $eligible[] = $m;
                }
            }
        }

        return sizeof($eligible) > 1;
    }
}

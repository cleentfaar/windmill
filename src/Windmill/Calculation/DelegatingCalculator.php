<?php

namespace App\Windmill\Calculation;

use App\Windmill\CheckState;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MoveCollection;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
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

    public function calculateWithDestination(Position $to, Game $game)
    {
        $remaining = new MoveCollection();

        foreach ($this->calculate($game)->all() as $move) {
            switch ($move::class) {
                case SimpleMove::class:
                    if ($move->to == $to) {
                        $remaining->add($move);
                    }
                    break;
                case MultiMove::class:
                    foreach ($move->to as $toInMove) {
                        if ($toInMove == $to) {
                            $remaining->add($move);
                        }
                    }
                    break;
            }
        }

        return $remaining;
    }

    public function calculateWithSource(Position $position, Game $game): MoveCollection
    {
        $moves = new MoveCollection();

        foreach ($this->calculate($game)->all() as $move) {
            $moveFrom = SimpleMove::class == $move::class ? $move->from : $move->from[0];

            if ($moveFrom == $position) {
                $moves->add($move);
            }
        }

        return $moves;
    }

    public function calculcateCheckState(AbstractMove $move, Game $game): CheckState
    {
        $moveFrom = SimpleMove::class == $move::class ? $move->from : $move->from[0];
        $movingPiece = $game->board->pieceOn($moveFrom);
        $clone = clone $game;
        $clone->move($move, true);
        $squaresWithOppositeKing = $clone->board->squaresWithPiece(King::class, Color::oppositeOf($movingPiece->color));

        if (1 != sizeof($squaresWithOppositeKing)) {
            throw new \Exception(sprintf('Expected a single square to be occupied, got %d', sizeof($squaresWithOppositeKing)));
        }

        $movesAttackingKing = $this->calculateWithDestination($squaresWithOppositeKing[0], $clone)->all();

        if (0 == sizeof($movesAttackingKing)) {
            return CheckState::NONE;
        }

        $clone = clone $game;
        $clone->move($move);

        foreach ($movesAttackingKing as $m) {
            return CheckState::CHECK; // stop here until checkmate logic is fixed
            $movesByKing = $this->calculateWithSource($squaresWithOppositeKing[0], $game)->all();

            if (0 == sizeof($movesByKing)) {
                // king has nowhere to go, checkmate
                return CheckState::CHECKMATE;
            }

            foreach ($movesByKing as $moveByKing) {
                $g = clone $clone;
                $g->move($moveByKing);
                $newKingDestination = SimpleMove::class == $moveByKing::class ? $moveByKing->to : $moveByKing->to[0];
                $movesStillAttackingKing = $this->calculateWithDestination($newKingDestination, $g)->all();

                if (sizeof($movesStillAttackingKing) > 0) {
                    return CheckState::CHECKMATE;
                }
            }

            return CheckState::CHECK;
        }

        return CheckState::NONE;
    }

    public function calculcateMultiplePiecesWithDestination(string $pieceClass, Position $destination, Game $game): bool
    {
        $moves = $this->calculateWithDestination($destination, $game)->all();
        $eligible = [];

        foreach ($moves as $m) {
            if (SimpleMove::class == $m::class && $game->board->pieceOn($m->from)::class == $pieceClass) {
                $eligible[] = $m;
            } elseif (MultiMove::class == $m::class) {
                foreach ($m->to as $x => $t) {
                    if ($t == $destination && $game->board->pieceOn($m->from[$x])::class == $pieceClass) {
                        $eligible[] = $m;
                    }
                }
            }
        }

        return sizeof($eligible) > 1;
    }
}

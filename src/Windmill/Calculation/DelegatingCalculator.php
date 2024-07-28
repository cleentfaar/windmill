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
        $movingPiece = $game->board->pieceOn($move->from[0]);
        $clone = clone $game;
        $clone->move($move, true);
        $squaresWithOppositeKing = $clone->board->squaresWithPiece(King::class, Color::oppositeOf($movingPiece->color));

        if (1 != sizeof($squaresWithOppositeKing)) {
            return CheckState::NONE;
        }

        $moveCollection = $this->calculate($clone);
        $movesAttackingKing = $moveCollection->to($squaresWithOppositeKing[0]);

        if (0 == sizeof($movesAttackingKing)) {
            return CheckState::NONE;
        }

        $clone = clone $game;
        $clone->move($move); // now king is checked, we need to see if he can escape

        $kingMoves = $this->calculate($clone);
        $canEscape = true;

        foreach ($kingMoves as $kingMove) {
            $c = clone $clone;
            $c->move($kingMove);
            $mc = $this->calculate($c);
            $sqk = $c->board->squaresWithPiece(King::class, Color::oppositeOf($movingPiece->color));
            $mk = $mc->to($sqk[0]);

            if (0 == sizeof($mk)) {
                // at least one way to escape
                $canEscape = true;
                break;
            } else {
                $canEscape = false;
            }
        }

        return $canEscape ? CheckState::CHECK : CheckState::CHECKMATE;
    }

    public function calculcateMultiplePiecesWithDestination(string $pieceClass, Position $destination, Game $game): bool
    {
        $moves = $this->calculate($game)->to($destination);
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

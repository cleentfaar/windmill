<?php

namespace App\Windmill;

use App\Windmill\Move\AbstractMove;
use App\Windmill\Move\MultiMove;
use App\Windmill\Move\SimpleMove;
use App\Windmill\Piece\AbstractPiece;
use Exception;
use Symfony\Component\Uid\Uuid;

class Board
{
    private array $squares = [
        Position::A1->value => null,
        Position::A2->value => null,
        Position::A3->value => null,
        Position::A4->value => null,
        Position::A5->value => null,
        Position::A6->value => null,
        Position::A7->value => null,
        Position::A8->value => null,

        Position::B1->value => null,
        Position::B2->value => null,
        Position::B3->value => null,
        Position::B4->value => null,
        Position::B5->value => null,
        Position::B6->value => null,
        Position::B7->value => null,
        Position::B8->value => null,

        Position::C1->value => null,
        Position::C2->value => null,
        Position::C3->value => null,
        Position::C4->value => null,
        Position::C5->value => null,
        Position::C6->value => null,
        Position::C7->value => null,
        Position::C8->value => null,

        Position::D1->value => null,
        Position::D2->value => null,
        Position::D3->value => null,
        Position::D4->value => null,
        Position::D5->value => null,
        Position::D6->value => null,
        Position::D7->value => null,
        Position::D8->value => null,

        Position::E1->value => null,
        Position::E2->value => null,
        Position::E3->value => null,
        Position::E4->value => null,
        Position::E5->value => null,
        Position::E6->value => null,
        Position::E7->value => null,
        Position::E8->value => null,

        Position::F1->value => null,
        Position::F2->value => null,
        Position::F3->value => null,
        Position::F4->value => null,
        Position::F5->value => null,
        Position::F6->value => null,
        Position::F7->value => null,
        Position::F8->value => null,

        Position::G1->value => null,
        Position::G2->value => null,
        Position::G3->value => null,
        Position::G4->value => null,
        Position::G5->value => null,
        Position::G6->value => null,
        Position::G7->value => null,
        Position::G8->value => null,

        Position::H1->value => null,
        Position::H2->value => null,
        Position::H3->value => null,
        Position::H4->value => null,
        Position::H5->value => null,
        Position::H6->value => null,
        Position::H7->value => null,
        Position::H8->value => null,
    ];

    public function __construct(
        public readonly Uuid $id,
        private readonly array $occupiedSquares = []
    ) {
        foreach ($this->occupiedSquares as $position => $piece) {
            $this->squares[$position] = $piece;
        }
    }

    public function squares(): array
    {
        return $this->squares;
    }

    public function pieceOn(Position $position): ?AbstractPiece
    {
        return $this->squares[$position->value] ?? null;
    }

    public function move(AbstractMove $move): void
    {
        switch ($move::class) {
            case SimpleMove::class:
                $piece = $this->pieceOn($move->from);
                $this->squares[$move->from->value] = null;
                $this->squares[$move->to->value] = $piece;
                break;
            case MultiMove::class:
                if ($move->comment == 'castle') {
//                    dump($move);
                }
                foreach ($move->to as $x => $to) {
                    if ($to == null) {
                        $from = $move->from[$x];
                        $this->squares[$from->value] = null;
                    }
                }

                foreach ($move->from as $x => $from) {
                    $to = $move->to[$x];

                    if ($to !== null) {
                        $movingPiece = $this->squares[$from->value];
                        if ($move->to[$x]) {
                            $this->squares[$move->to[$x]->value] = $movingPiece;
                        }

                        $this->squares[$from->value] = null;
                    }
                }
                if ($move->comment == 'castle') {
//                    dump($this->squares);exit;
                }
                break;
            default:
                throw new Exception(sprintf("Unsupported move class: %s", $move::class));
        }
    }
}

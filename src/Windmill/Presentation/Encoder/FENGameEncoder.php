<?php

namespace App\Windmill\Presentation\Encoder;

use App\Windmill\Board;
use App\Windmill\CastlingAvailability;
use App\Windmill\Color;
use App\Windmill\Game;
use App\Windmill\Piece;
use App\Windmill\PieceType;
use App\Windmill\PlayerInterface;
use App\Windmill\Position;
use App\Windmill\State;
use Symfony\Component\Uid\Uuid;

class FENGameEncoder implements GameEncoderInterface
{
    public const STANDARD_FEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
    private const FEN_FIELDS = [
        'piece placement data' => 0,
        'active color' => 1,
        'castling availability' => 2,
        'en passant target square' => 3,
        'halfmove clock' => 4,
        'fullmove clock' => 5,
    ];

    public function encode(Game $game): string
    {
        $fields = [
            $this->encodePiecePlacementData($game),
            $this->encodeActiveColor($game),
            $this->encodeCastlingAvailability($game),
            $this->encodeEnPassantTargetSquare($game),
            $this->encodeHalfmoveClock($game),
            $this->encodeFullmoveClock($game),
        ];

        $output = implode(' ', $fields);

        return $output;
    }

    public function decode(string $encodedGame, PlayerInterface $whitePlayer, PlayerInterface $blackPlayer, ?Uuid $id = null, ?Uuid $boardId = null): Game
    {
        $encodedGame = trim($encodedGame);
        $piecePlacementData = $this->decodePiecePlacementData($encodedGame, $boardId);
        $activeColor = $this->decodeActiveColor($encodedGame);
        $castlingAvailability = $this->decodeCastlingAvailability($encodedGame);
        $enPassantTargetSquare = $this->decodeEnPassantTargetSquare($encodedGame);
        $halfMoveClock = $this->decodeHalfMoveClock($encodedGame);
        $fullMoveClock = $this->decodeFullMoveClock($encodedGame);

        return new Game(
            $id ?: Uuid::v4(),
            State::STARTED,
            $piecePlacementData,
            $whitePlayer,
            $blackPlayer,
            $activeColor,
            $castlingAvailability,
            $enPassantTargetSquare,
            $halfMoveClock,
            $fullMoveClock,
        );
    }

    private function encodeCastlingAvailability(Game $game): string
    {
        if (
            !$game->castlingAvailability->whiteCanCastleKingside
            && !$game->castlingAvailability->whiteCanCastleQueenside
            && !$game->castlingAvailability->blackCanCastleKingside
            && !$game->castlingAvailability->blackCanCastleQueenside
        ) {
            return '-';
        }

        $output = '';
        $output .= $game->castlingAvailability->whiteCanCastleKingside ? 'K' : '';
        $output .= $game->castlingAvailability->whiteCanCastleQueenside ? 'Q' : '';
        $output .= $game->castlingAvailability->blackCanCastleKingside ? 'k' : '';
        $output .= $game->castlingAvailability->blackCanCastleQueenside ? 'q' : '';

        return $output;
    }

    private function encodeEnPassantTargetSquare(Game $game): string
    {
        if ($position = $game->enPassantTargetSquare()) {
            return $position->fileLetter().$position->rank();
        }

        return '-';
    }

    private function encodeActiveColor(Game $game): string
    {
        return Color::WHITE == $game->currentColor() ? 'w' : 'b';
    }

    private function encodePiecePlacementData(Game $game): string
    {
        $output = '';

        for ($rank = 8; $rank >= 1; --$rank) {
            $emptyCounter = 0;

            for ($file = 1; $file <= 8; ++$file) {
                $position = Position::fromFileAndRank($file, $rank);
                if ($piece = $game->board->pieceOn($position)) {
                    $output .= $emptyCounter > 0 ? $emptyCounter : '';
                    $emptyCounter = 0;
                    $output .= $this->encodePiece($piece);
                } else {
                    ++$emptyCounter;
                }

                if (8 == $file) {
                    $output .= $emptyCounter > 0 ? $emptyCounter : '';
                }
            }

            if ($rank > 1) {
                $output .= '/';
            }
        }

        return $output;
    }

    private function encodeHalfmoveClock(Game $game): string
    {
        return (string) $game->halfMoveClock();
    }

    private function encodeFullmoveClock(Game $game): string
    {
        return (string) $game->fullMoveClock();
    }

    private function decodePiecePlacementData(string $fen, ?Uuid $boardId = null): Board
    {
        $piecePlacement = explode(' ', $fen)[self::FEN_FIELDS['piece placement data']];
        $rank = 8;
        $file = 0;
        $squares = [];

        foreach (mb_str_split($piecePlacement) as $f) {
            ++$file;

            switch (mb_strtolower($f)) {
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                    $file += ($f - 1);
                    break;
                case 'p':
                    $squares[$file.$rank] = new Piece(ctype_upper($f) ? Color::WHITE : Color::BLACK, PieceType::PAWN);
                    break;
                case 'b':
                    $squares[$file.$rank] = new Piece(ctype_upper($f) ? Color::WHITE : Color::BLACK, PieceType::BISHOP);
                    break;
                case 'n':
                    $squares[$file.$rank] = new Piece(ctype_upper($f) ? Color::WHITE : Color::BLACK, PieceType::KNIGHT);
                    break;
                case 'r':
                    $squares[$file.$rank] = new Piece(ctype_upper($f) ? Color::WHITE : Color::BLACK, PieceType::ROOK);
                    break;
                case 'q':
                    $squares[$file.$rank] = new Piece(ctype_upper($f) ? Color::WHITE : Color::BLACK, PieceType::QUEEN);
                    break;
                case 'k':
                    $squares[$file.$rank] = new Piece(ctype_upper($f) ? Color::WHITE : Color::BLACK, PieceType::KING);
                    break;
                case '/':
                    $file = 0;
                    --$rank;
                    break;
            }
        }

        return new Board($boardId ?: Uuid::v4(), $squares);
    }

    private function decodeActiveColor(string $fen): Color
    {
        $activeColor = explode(' ', $fen)[self::FEN_FIELDS['active color']];

        return 'w' == $activeColor ? Color::WHITE : Color::BLACK;
    }

    private function decodeCastlingAvailability(string $fen): CastlingAvailability
    {
        $castlingNotation = explode(' ', $fen)[self::FEN_FIELDS['castling availability']];

        return new CastlingAvailability(
            str_contains($castlingNotation, 'K'),
            str_contains($castlingNotation, 'Q'),
            str_contains($castlingNotation, 'k'),
            str_contains($castlingNotation, 'q'),
        );
    }

    private function decodeEnPassantTargetSquare(string $encodedGame): ?Position
    {
        $square = explode(' ', $encodedGame)[self::FEN_FIELDS['en passant target square']];

        if ('-' == $square) {
            return null;
        }

        return Position::fromFileLetterAndRank(...str_split($square));
    }

    private function decodeHalfMoveClock(string $encodedGame): int
    {
        return (int) explode(' ', $encodedGame)[self::FEN_FIELDS['halfmove clock']];
    }

    private function decodeFullMoveClock(string $encodedGame): int
    {
        return (int) explode(' ', $encodedGame)[self::FEN_FIELDS['fullmove clock']];
    }

    private function encodePiece(Piece $piece): string
    {
        return match ($piece->type) {
            PieceType::PAWN => Color::WHITE == $piece->color ? 'P' : 'p',
            PieceType::BISHOP => Color::WHITE == $piece->color ? 'B' : 'b',
            PieceType::KNIGHT => Color::WHITE == $piece->color ? 'N' : 'n',
            PieceType::ROOK => Color::WHITE == $piece->color ? 'R' : 'r',
            PieceType::QUEEN => Color::WHITE == $piece->color ? 'Q' : 'q',
            PieceType::KING => Color::WHITE == $piece->color ? 'K' : 'k',
        };
    }
}

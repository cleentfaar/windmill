<?php

namespace App\Windmill;

enum Position: int
{
	case A1 = 11;
	case B1 = 21;
	case C1 = 31;
	case D1 = 41;
	case E1 = 51;
	case F1 = 61;
	case G1 = 71;
	case H1 = 81;

	case A2 = 12;
	case B2 = 22;
	case C2 = 32;
	case D2 = 42;
	case E2 = 52;
	case F2 = 62;
	case G2 = 72;
	case H2 = 82;

	case A3 = 13;
	case B3 = 23;
	case C3 = 33;
	case D3 = 43;
	case E3 = 53;
	case F3 = 63;
	case G3 = 73;
	case H3 = 83;

	case A4 = 14;
	case B4 = 24;
	case C4 = 34;
	case D4 = 44;
	case E4 = 54;
	case F4 = 64;
	case G4 = 74;
	case H4 = 84;

	case A5 = 15;
	case B5 = 25;
	case C5 = 35;
	case D5 = 45;
	case E5 = 55;
	case F5 = 65;
	case G5 = 75;
	case H5 = 85;

	case A6 = 16;
	case B6 = 26;
	case C6 = 36;
	case D6 = 46;
	case E6 = 56;
	case F6 = 66;
	case G6 = 76;
	case H6 = 86;

	case A7 = 17;
	case B7 = 27;
	case C7 = 37;
	case D7 = 47;
	case E7 = 57;
	case F7 = 67;
	case G7 = 77;
	case H7 = 87;

	case A8 = 18;
	case B8 = 28;
	case C8 = 38;
	case D8 = 48;
	case E8 = 58;
	case F8 = 68;
	case G8 = 78;
	case H8 = 88;

	private const RANK_LETTERS = [
		1 => 'a',
		2 => 'b',
		3 => 'c',
		4 => 'd',
		5 => 'e',
		6 => 'f',
		7 => 'g',
		8 => 'h',
	];

	public static function fromFileAndRank(int $file, int $rank): ?self
	{
		return self::tryFrom(intval($file.''.$rank));
	}

	public function file(): int
	{
		return substr($this->value, 0, 1);
	}

	public function rank(): int
	{
		return substr($this->value, 1, 1);
	}

	public function fileLetter(): string
	{
		return self::RANK_LETTERS[$this->file()];
	}

	public static function fromFileLetterAndRank(string $letter, int $rank): self
	{
		$file = array_flip(self::RANK_LETTERS)[$letter];

		return self::tryFrom($file.$rank);
	}
}

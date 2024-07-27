<?php

namespace App\Windmill\Engine;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Game;
use App\Windmill\Move;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyConsoleHuman implements RecommendationEngineInterface
{
    public function __construct(
        private readonly InputInterface $input,
        private readonly ConsoleSectionOutput $questionSection,
        private readonly DelegatingCalculator $calculator = new DelegatingCalculator(),
        private readonly AlgebraicMoveEncoder $moveEncoder = new AlgebraicMoveEncoder(),
    ) {
    }

    public function recommend(Game $game): Recommendation
    {
        return new Recommendation(
            $this->askForNextMove($game, $this->input, $this->questionSection),
            100
        );
    }

    private function askForNextMove(Game $game, InputInterface $input, ConsoleSectionOutput $section): Move
    {
        $moves = $this->calculator->calculate($game);
        $moveFENs = $moves->map(function (Move $move) use ($game) {
            return $this->moveEncoder->encode($move, $game);
        });

        $io = new SymfonyStyle($input, $section);
        $question = $this->createMoveQuestion($game, $moveFENs);

        do {
            $answer = $io->askQuestion($question);
            $valid = in_array($answer, $moveFENs);
            $section->clear();
        } while (false == $valid);

        return $this->moveEncoder->decode($answer, $game);
    }

    private function createMoveQuestion(Game $game, array $moveFENs): Question
    {
        $question = new Question(
            sprintf(
                "%s to play. Please enter your next move.\nHere are a few options: %s",
                $game->currentColor()->name(),
                implode(', ', $moveFENs)
            ),
        );

        $question->setAutocompleterValues($moveFENs);

        return $question;
    }
}

<?php

namespace App\Command;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Engine\Random;
use App\Windmill\Game;
use App\Windmill\GameFactory;
use App\Windmill\Move\AbstractMove;
use App\Windmill\Persistence\GameRepository;
use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;
use App\Windmill\Presentation\Encoder\SymfonyConsoleBoardEncoder;
use App\Windmill\Presentation\Encoder\VerboseMoveEncoder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'windmill:play',
	description: 'Play a game of chess against the computer',
)]
class WindmillPlayCommand extends Command
{
	public function __construct(
		private readonly GameFactory $gameFactory,
		private readonly GameRepository $gameRepository,
		private readonly SymfonyConsoleBoardEncoder $encoder,
		private readonly DelegatingCalculator $calculator,
		private readonly AlgebraicMoveEncoder $moveEncoder,
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->addArgument('engine', InputArgument::OPTIONAL, 'The engine that should be used by the computer', 'random')
			->addOption('color', null, InputOption::VALUE_REQUIRED, 'The color you would like to play as ', 'WHITE')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$boardSection = $output->section();
		$lastMoveSection = $output->section();
		$questionSection = $output->section();

		$game = $this->gameFactory->standard(
			$input->getOption('color') == Color::WHITE->name ? 'Human' : 'Computer',
			$input->getOption('color') == Color::WHITE->name ? null : $input->getArgument('engine'),
			$input->getOption('color') == Color::BLACK->name ? 'Human' : 'Computer',
			$input->getOption('color') == Color::BLACK->name ? null : $input->getArgument('engine'),
		);

		while (!$game->isFinished()) {
			$boardSection->overwrite($this->encoder->encode($game->board));
			$engine = $game->currentPlayer()->engine;

			if (!$engine) {
				$move = $this->askForNextMove($game, $input, $questionSection);
			} else {
				switch ($engine) {
					case 'random':
						$move = (new Random($this->calculator))->recommend($game)->move;
						break;
					default:
						throw new \Exception(sprintf('Unsupported engine: %s', $engine));
				}
			}

			$lastMoveSection->overwrite(sprintf(
				'%s played: %s',
				$game->currentColor()->name,
				(new VerboseMoveEncoder())->encode($move, $game),
			));

			$game->move($move);
		}

		return Command::SUCCESS;
	}

	private function askForNextMove(Game $game, InputInterface $input, ConsoleSectionOutput $section): AbstractMove
	{
		$moves = $this->calculator->calculate($game);
		$moveFENs = array_map(function (AbstractMove $move) use ($game) {
			return $this->moveEncoder->encode($move, $game);
		}, $moves->all());

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

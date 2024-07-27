<?php

namespace App\Command;

use App\Windmill\Calculation\DelegatingCalculator;
use App\Windmill\Color;
use App\Windmill\Engine\Random;
use App\Windmill\Engine\SymfonyConsoleHuman;
use App\Windmill\GameFactory;
use App\Windmill\Presentation\Encoder\SymfonyConsoleBoardEncoder;
use App\Windmill\Presentation\Encoder\VerboseMoveEncoder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'windmill:play',
    description: 'Play a game of chess against the computer',
)]
class WindmillPlayCommand extends Command
{
    public function __construct(
        private readonly GameFactory $gameFactory,
        private readonly SymfonyConsoleBoardEncoder $encoder,
        private readonly DelegatingCalculator $calculator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('color', 'c', InputOption::VALUE_REQUIRED, 'The color you would like to play as ', Color::WHITE->name)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $boardSection = $output->section();
        $lastMoveSection = $output->section();
        $questionSection = $output->section();

        $game = $this->gameFactory->standard(
            $input->getOption('color') == Color::WHITE->name ? 'Human' : 'Computer',
            $input->getOption('color') == Color::WHITE->name ? new SymfonyConsoleHuman($input, $questionSection) : new Random($this->calculator),
            $input->getOption('color') == Color::BLACK->name ? 'Human' : 'Computer',
            $input->getOption('color') == Color::BLACK->name ? new SymfonyConsoleHuman($input, $questionSection) : new Random($this->calculator),
        );

        while (!$game->isFinished()) {
            $boardSection->overwrite($this->encoder->encode($game->board));
            $move = $game->currentPlayer()->engine->recommend($game)->move;

            $lastMoveSection->overwrite(sprintf(
                '%s played: %s',
                $game->currentColor()->name,
                (new VerboseMoveEncoder())->encode($move, $game),
            ));

            $game->move($move);
        }

        return Command::SUCCESS;
    }
}

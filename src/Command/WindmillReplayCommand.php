<?php

namespace App\Command;

use App\Windmill\Presentation\Encoder\AlgebraicMoveEncoder;
use App\Windmill\Presentation\Encoder\FENGameEncoder;
use App\Windmill\Presentation\Encoder\PgnReplayEncoder;
use App\Windmill\Presentation\Encoder\SymfonyConsoleBoardEncoder;
use App\Windmill\Presentation\Encoder\VerboseMoveEncoder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'windmill:replay',
    description: 'Replays a game of chess from a given PGN file',
)]
class WindmillReplayCommand extends Command
{
    public function __construct(
        private readonly SymfonyConsoleBoardEncoder $boardEncoder,
        private readonly PgnReplayEncoder $replayEncoder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('path_to_pgn', InputArgument::REQUIRED, 'The path to a PGN file that should be replayed')
            ->addOption('skip-failures', 's', InputOption::VALUE_NONE, 'Flag to run the replay up to the point errors occur, instead of failing immediately')
            ->addOption('fen-only', 'f', InputOption::VALUE_NONE, 'Flag to only display the moves by their SEN notation and FEN result as PHP arrays (debugging and testing purposes)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $boardSection = $output->section();
        $lastMoveSection = $output->section();
        $confirmSection = $output->section();
        $replay = $this->replayEncoder->decode(
            $input->getArgument('path_to_pgn'),
            $input->getOption('skip-failures')
        );
        $backupReplay = $this->replayEncoder->decode(
            $input->getArgument('path_to_pgn'),
            $input->getOption('skip-failures')
        );
        $game = $replay->game;
        $backupGame = $backupReplay->game;
        $gameEncoder = new FENGameEncoder();

        foreach ($replay->moves as $move) {
            $currentColor = $game->currentColor();
            $game->move($move);
            if ($input->getOption('fen-only')) {
                $output->writeln(sprintf(
                    '[\'%s\', \'%s\'],',
                    (new AlgebraicMoveEncoder())->encode($move, $backupGame),
                    $gameEncoder->encode($game)
                ));

                $backupGame->move($move);
            } else {
                $lastMoveSection->overwrite(sprintf(
                    '%s played: %s (SEN: %s, FEN: %s)',
                    $currentColor->name,
                    (new VerboseMoveEncoder())->encode($move, $backupGame),
                    (new AlgebraicMoveEncoder())->encode($move, $backupGame),
                    $gameEncoder->encode($game)
                ));

                $backupGame->move($move);

                $boardSection->overwrite($this->boardEncoder->encode($game->board));

                $continue = $this->askToContinue($input, $confirmSection);

                if (!$continue) {
                    break;
                }
            }
        }

        return Command::SUCCESS;
    }

    private function askToContinue(InputInterface $input, ConsoleSectionOutput $confirmSection): bool
    {
        $io = new SymfonyStyle($input, $confirmSection);

        $continue = $io->confirm('Continue?');
        $confirmSection->clear();

        return $continue;
    }
}

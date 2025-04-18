<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\TornApiService;
use App\Service\TornUserService;

#[AsCommand(
    name: 'app:load-torn-attack-date',
    description: 'Add a short description for your command',
)]
class LoadTornAttackDateCommand extends Command
{
    public function __construct(private TornApiService $tornAPI, private TornUserService $tornUserService)
    {
        // The parent constructor is called to ensure proper initialization of the command
        // and to allow for dependency injection.
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }
        $self = $this->tornAPI->getUser();
        if (isset($self['player_id'])) {
            $myId = $self['player_id'];
        }
        $attacks = $this->tornAPI->getAttacks();
        if (isset($attacks['attacks'])) {
            foreach ($attacks['attacks'] as $attack) {
                if ($attack['defender_id'] == $myId) {
                    continue;
                }
                try {
                    $this->tornUserService->addAttackFromJson($attack);
                } catch (\Exception $e) {
                    $newUser = $this->tornAPI->getUser($attack['defender_id']);
                    $this->tornUserService->addUserFromJson($newUser);
                    $this->tornUserService->addAttackFromJson($attack);
                }
                echo "Loaded Attack: " . $attack['attacker_name'] . " vs " . $attack['defender_name'] . "\n";
            }
        }

        return Command::SUCCESS;
    }
}

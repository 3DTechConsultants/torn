<?php

namespace App\Command;

use App\Service\TornApiService;
use App\Service\TornUserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Mug;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;

#[AsCommand(
    name: 'app:load-mugs',
    description: 'Add a short description for your command',
)]
class LoadMugsCommand extends Command
{
    public function __construct(private TornUserService $tus, private TornApiService $tapi, private EntityManagerInterface $em)
    {
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


        $logs = $this->tapi->getLog();
        foreach ($logs['log'] as $logId => $log) {
            if ($log['log'] !== 8155) {
                continue;
            }

            // if ($log['data']['money_mugged'] <= 20000) {
            //     continue;
            // }

            $existingMug = $this->em->getRepository(Mug::class)->findOneBy(['tornMugId' => $logId]);
            if ($existingMug) {
                //$output->writeln("Mug with ID $logId already exists, skipping.");
                continue;
            }
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($log['timestamp']);
            $mug = new Mug();
            $mug->setTornMugId($logId)
                ->setDateTime($dateTime)
                ->setDefender($this->tus->getTornUser($log['data']['defender']))
                ->setMoneyMugged($log['data']['money_mugged'] ?? 0);
            $this->em->persist($mug);
            $output->writeln("Mug with ID $logId has been added.");
        }
        $this->em->flush();
        return Command::SUCCESS;
    }
}

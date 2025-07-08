<?php

namespace App\Command;

use App\Service\TornApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Bounty;

#[AsCommand(
    name: 'app:find-bounty',
    description: 'Add a short description for your command',
)]
class FindBountyCommand extends Command
{
    public function __construct(private TornApiService $tas, private EntityManagerInterface $em)
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
        $bounties = $this->tas->listBounties();
        $elo = $highestBeaten = $age = $factionName = null;
        foreach ($bounties as $bounty) {
            if ($bounty['target_level'] > 20) {
                continue;
            }
            if ($bounty['reward'] < 100000) {
                continue;
            }
            $userId = $bounty['target_id'];
            print $bounty['quantity'] . " " . $bounty['target_name'] . " (" . $bounty['target_id'] . ") - ";
            $user = $this->tas->getUser($userId, ['basic', 'personalstats']);

            if ($user['status']['state'] != 'Okay') {
                continue;
            }

            if (isset($user['faction']['faction_name'])) {
                $factionName = $user['faction']['faction_name'];
            }

            if (isset($user['personalstats']['elo'])) {
                $elo = $user['personalstats']['elo'];
            }

            if (isset($user['personalstats']['highestbeaten'])) {
                $highestBeaten = $user['personalstats']['highestbeaten'];
            }
            if (isset($user['age'])) {
                $age = $user['age'];
            }

            if ($factionName && $elo && $highestBeaten && $age) {
                $newBounty = new Bounty();
                $newBounty->setTargetId($bounty['target_id'])
                    ->setTargetName($bounty['target_name'])
                    ->setTargetLevel($bounty['target_level'])
                    ->setFaction($factionName)
                    ->setTargetELO($elo)
                    ->setTargetHLD($highestBeaten)
                    ->setTargetAge($age)
                    ->setreward($bounty['reward']);

                $this->em->persist($newBounty);
                $this->em->flush();
                print ".";
            }
        }
        print "\n";
        return Command::SUCCESS;
    }
}

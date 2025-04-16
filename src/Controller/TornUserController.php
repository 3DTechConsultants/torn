<?php

namespace App\Controller;

use App\Repository\TornAttackRepository;
use App\Repository\TornUserRepository;
use App\Service\TornApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TornUserService;

final class TornUserController extends AbstractController
{
    #[Route('/', name: 'app_torn_user')]
    public function index(TornUserRepository $tur, TornApiService $tas, TornAttackRepository $tar): Response
    {
        $self = $tas->getUser();
        $myId = $self['player_id'] ?? null;
        $myTornUser = $tur->findOneBy(['tornId' => $myId]);

        return $this->render('torn_user/index.html.twig', [
            'users' => array_filter($tur->findAll(), fn($user) => $user->getPlayDuration() > 0),
            'ammos' => $tas->getAmmo(),
            'attacks' => $tar->findBy(['defender' => $myTornUser], ['dateTimeStarted' => 'DESC']),
        ]);
    }

    #[Route('/employed', name: 'app_torn_user_employed')]
    public function employed(TornUserRepository $tur, TornApiService $tas, TornAttackRepository $tar): Response
    {
        $self = $tas->getUser();
        $myId = $self['player_id'] ?? null;
        return $this->render('torn_user/index.html.twig', [
            'users' => array_filter($tur->findAll(), fn($user) => $user->getPlayDuration() > 0 && $user->getJob() !== 'None'),
            'ammos' => $tas->getAmmo(),
            'attacks' => $tar->findBy(['defender' => $myId], ['dateTimeStarted' => 'DESC']),
        ]);
    }

    #[Route('/load-attacks', name: 'app_load_attacks')]
    public function loadAttacks(TornUserRepository $tur, TornApiService $tornApiService, TornUserService $tornUserService): Response
    {
        $attacks = $tornApiService->getAttacks();
        $newAttacksCount = 0;

        if (isset($attacks['attacks'])) {
            foreach ($attacks['attacks'] as $attack) {
                $defender = $tur->findOneBy(['tornId' => $attack['defender_id']]);
                if (!$defender) {
                    $defender = $tornApiService->getUser($attack['defender_id']);
                    $tornUserService->addUserFromJson($defender);
                }

                $attacker = $tur->findOneBy(['tornId' => $attack['attacker_id']]);
                if (!$attacker) {
                    $attacker = $tornApiService->getUser($attack['attacker_id']);
                    $tornUserService->addUserFromJson($attacker);
                }
                $added = $tornUserService->addAttackFromJson($attack);
                if ($added) {
                    $newAttacksCount++;
                }
            }
        }
        if ($newAttacksCount === 0) {
            $this->addFlash('notice', 'No new attacks were found.');
        } else {
            $this->addFlash('notice', "$newAttacksCount new attacks were added.");
        }
        return $this->redirectToRoute('app_torn_user');
    }
}

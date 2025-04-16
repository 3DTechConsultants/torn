<?php

namespace App\Controller;

use App\Repository\TornUserRepository;
use App\Service\TornApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TornUserService;

final class TornUserController extends AbstractController
{
    #[Route('/', name: 'app_torn_user')]
    public function index(TornUserRepository $tur): Response
    {
        return $this->render('torn_user/index.html.twig', [
            'users' => array_filter($tur->findAll(), fn($user) => $user->getPlayDuration() > 0)
        ]);
    }

    #[Route('/load-attacks', name: 'app_load_attacks')]
    public function loadAttacks(TornUserRepository $tur, TornApiService $tornApiService, TornUserService $tornUserService): Response
    {
        $self = $tornApiService->getUser();
        $myId = $self['player_id'] ?? null;

        if (!$myId) {
            $this->addFlash('notice', 'Failed to retrieve your user ID from the Torn API.');
            return $this->redirectToRoute('app_torn_user');
        }

        $attacks = $tornApiService->getAttacks();
        $newAttacksCount = 0;

        if (isset($attacks['attacks'])) {
            foreach ($attacks['attacks'] as $attack) {
                if ($attack['defender_id'] == $myId) {
                    continue; // Skip attacks where the user is the defender
                }
                $defender = $tur->findOneBy(['tornId' => $attack['defender_id']]);
                if ($defender) {
                    $lastAttack = new \DateTime('@' . $attack['timestamp_started']);
                    if ($defender->getLastAttackDate() >= $lastAttack) {
                        continue; // Skip if the defender's last attack is more recent than the current attack
                    }
                    $tornUserService->addAttackFromJson($attack);
                    $newAttacksCount++;
                    continue;
                } else {
                    $newUser = $tornApiService->getUser($attack['defender_id']);
                    $tornUserService->addUserFromJson($newUser);
                    $tornUserService->addAttackFromJson($attack);
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

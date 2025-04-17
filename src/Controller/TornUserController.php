<?php

namespace App\Controller;

use App\Repository\TornAttackRepository;
use App\Repository\TornUserRepository;
use App\Service\TornApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TornUserService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

final class TornUserController extends AbstractController
{
    #[Route('/', name: 'app_torn_user')]
    public function index(
        TornUserRepository $tornUserRepository,
        TornApiService $tapi,
        TornAttackRepository $tornAttackRepository,
        RequestStack $rs,
        #[MapQueryParameter] bool $employed = false
    ): Response {
        $myId = $rs->getSession()->get('tornUserId');
        if (!$myId) {
            $self = $tapi->getUser();
            $myId = $self['player_id'] ?? null;
            $rs->getSession()->set('tornUserId', $myId);
        }

        $myTornUser = $tornUserRepository->findOneBy(['tornId' => $myId]);

        if ($employed) {
            $users = array_filter($tornUserRepository->findAll(), fn($user) => $user->getPlayDuration() > 0 && $user->getJob() !== 'None' && $user->getTornId() !== $myId);
        } else {
            $users = array_filter($tornUserRepository->findAll(), fn($user) => $user->getPlayDuration() > 0 && $user->getTornId() !== $myId);
        }

        return $this->render('torn_user/index.html.twig', [
            'users' => $users,
            'ammos' => $tapi->getAmmo(),
            'attacks' => $tornAttackRepository->findBy(['defender' => $myTornUser], ['dateTimeStarted' => 'DESC']),
            'employed' => $employed,
        ]);
    }


    #[Route('/load-attacks', name: 'app_load_attacks')]
    public function loadAttacks(TornUserRepository $tornUserRepository, TornApiService $tapi, TornUserService $tornUserService): Response
    {
        $attacks = $tapi->getAttacks();
        $newAttacksCount = 0;

        if (isset($attacks['attacks'])) {
            foreach ($attacks['attacks'] as $attack) {
                $defender = $tornUserRepository->findOneBy(['tornId' => $attack['defender_id']]);
                if (!$defender) {
                    $defender = $tapi->getUser($attack['defender_id']);
                    $tornUserService->addUserFromJson($defender);
                }

                $attacker = $tornUserRepository->findOneBy(['tornId' => $attack['attacker_id']]);
                if (!$attacker) {
                    $attacker = $tapi->getUser($attack['attacker_id']);
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

<?php

namespace App\Controller;

use App\Repository\MugRepository;
use App\Repository\TornUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MugController extends AbstractController
{
    #[Route('/mug', name: 'app_mug')]
    public function index(TornUserRepository $tur, MugRepository $mr): Response
    {

        return $this->render('mug/index.html.twig', [
            'users' => $tur->findMuggedUsers(),
            'totalMugged' => $mr->getTotalMoneyMugged(),
        ]);
    }
}

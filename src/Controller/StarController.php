<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TornUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class StarController extends AbstractController
{
    #[Route('/star/toggle', name: 'app_star_toggle', methods: ['GET'])]
    public function toggleStar(Request $request, TornUserRepository $tornUserRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $id = $request->query->get('id');

        if (!$id) {
            return new JsonResponse(['error' => 'ID parameter is missing'], Response::HTTP_BAD_REQUEST);
        }

        $user = $tornUserRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Toggle the isStarred value
        $user->setStarred(!$user->isStarred());

        // Persist the change to the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Return the new status
        return new JsonResponse(['success' => true, 'isStarred' => $user->isStarred()]);
    }
}

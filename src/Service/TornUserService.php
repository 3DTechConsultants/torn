<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TornUser;
use DateTime;

class TornUserService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addUserFromJson(array $userData): void
    {
        try {
            // Validate required fields in the input data
            if (!isset($userData['level'], $userData['player_id'], $userData['name'], $userData['signup'], $userData['status'], $userData['life'], $userData['job'], $userData['last_action'])) {
                throw new \InvalidArgumentException('Invalid user data provided. Missing required keys.');
            }

            // Check if the user already exists in the database
            $existingUser = $this->entityManager->getRepository(TornUser::class)->findOneBy(['tornId' => $userData['player_id']]);
            if ($existingUser) {
                throw new \RuntimeException('User already exists in the database.');
            }

            // Create a new User entity
            $user = new TornUser();
            $user->setName($userData['name'])
                ->setTornId($userData['player_id'])
                ->setLevel($userData['level'])
                ->setSignupDate(new \DateTime($userData['signup']))
                ->setStatus($userData['status']['description'] ?? 'Unknown')
                ->setLife($userData['life']['maximum'] ?? 0)
                ->setJob($userData['job']['position'] ?? 'Unknown')
                ->setGender($userData['gender'])
                ->setLastAction(new \DateTime('@' . $userData['last_action']['timestamp'] ?? 'now'));

            // Persist the new user to the database
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while creating the user: ' . $e->getMessage());
        }
    }

    public function addAttackFromJson(array $attack): void
    {
        // Validate required fields in the input data
        if (!isset($attack['attacker_id'], $attack['defender_id'], $attack['timestamp_started'])) {
            throw new \InvalidArgumentException('Invalid attack data provided. Missing required keys.');
        }

        $tornUser = $this->entityManager->getRepository(TornUser::class)->findOneBy(['tornId' => $attack['defender_id']]);

        if (!$tornUser) {
            throw new \RuntimeException('User not found in the database.');
        }

        $lastAttack = new DateTime('@' . $attack['timestamp_started']);
        $tornUser->setLastAttackDate($lastAttack);

        $this->entityManager->flush();
    }
    public function getNextTornUserId(): int
    {
        // Fetch the maximum tornId from the database
        $lastTornUser = $this->entityManager->getRepository(TornUser::class)->findLastTornUser();
        if ($lastTornUser === null) {
            return 2001000; // If no users exist, start with ID 1
        }
        return $lastTornUser->getTornId() + 1;
    }
}

<?php

namespace App\Entity;

use App\Repository\BountyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BountyRepository::class)]
class Bounty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $targetName = null;

    #[ORM\Column]
    private ?int $targetLevel = null;

    #[ORM\Column]
    private ?int $targetId = null;

    #[ORM\Column]
    private ?int $reward = null;

    #[ORM\Column]
    private ?int $targetAge = null;

    #[ORM\Column]
    private ?int $targetELO = null;

    #[ORM\Column]
    private ?int $targetHLD = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $faction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTargetName(): ?string
    {
        return $this->targetName;
    }

    public function setTargetName(string $targetName): static
    {
        $this->targetName = $targetName;

        return $this;
    }

    public function getTargetLevel(): ?int
    {
        return $this->targetLevel;
    }

    public function setTargetLevel(int $targetLevel): static
    {
        $this->targetLevel = $targetLevel;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): static
    {
        $this->targetId = $targetId;

        return $this;
    }

    public function getReward(): ?int
    {
        return $this->reward;
    }

    public function setReward(int $reward): static
    {
        $this->reward = $reward;

        return $this;
    }

    public function getTargetAge(): ?int
    {
        return $this->targetAge;
    }

    public function setTargetAge(int $targetAge): static
    {
        $this->targetAge = $targetAge;

        return $this;
    }

    public function getTargetELO(): ?int
    {
        return $this->targetELO;
    }

    public function setTargetELO(int $targetELO): static
    {
        $this->targetELO = $targetELO;

        return $this;
    }

    public function getTargetHLD(): ?int
    {
        return $this->targetHLD;
    }

    public function setTargetHLD(int $targetHLD): static
    {
        $this->targetHLD = $targetHLD;

        return $this;
    }

    public function getFaction(): ?string
    {
        return $this->faction;
    }

    public function setFaction(?string $faction): static
    {
        $this->faction = $faction;

        return $this;
    }
}

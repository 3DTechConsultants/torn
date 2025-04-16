<?php

namespace App\Entity;

use App\Repository\TornAttackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TornAttackRepository::class)]
class TornAttack
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTimeStarted = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTimeEnded = null;

    #[ORM\Column(length: 255)]
    private ?string $result = null;

    #[ORM\ManyToOne(inversedBy: 'attacker')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TornUser $attacker = null;

    #[ORM\ManyToOne(inversedBy: 'defender')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TornUser $defender = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $tornAttackCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTimeStarted(): ?\DateTimeInterface
    {
        return $this->dateTimeStarted;
    }

    public function setDateTimeStarted(\DateTimeInterface $dateTimeStarted): static
    {
        $this->dateTimeStarted = $dateTimeStarted;

        return $this;
    }

    public function getDateTimeEnded(): ?\DateTimeInterface
    {
        return $this->dateTimeEnded;
    }

    public function setDateTimeEnded(\DateTimeInterface $dateTimeEnded): static
    {
        $this->dateTimeEnded = $dateTimeEnded;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function getAttacker(): ?TornUser
    {
        return $this->attacker;
    }

    public function setAttacker(?TornUser $attacker): static
    {
        $this->attacker = $attacker;

        return $this;
    }

    public function getDefender(): ?TornUser
    {
        return $this->defender;
    }

    public function setDefender(?TornUser $defender): static
    {
        $this->defender = $defender;

        return $this;
    }

    public function getTornAttackCode(): ?string
    {
        return $this->tornAttackCode;
    }

    public function setTornAttackCode(string $tornAttackCode): static
    {
        $this->tornAttackCode = $tornAttackCode;

        return $this;
    }
}

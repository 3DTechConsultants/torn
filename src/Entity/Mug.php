<?php

namespace App\Entity;

use App\Repository\MugRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MugRepository::class)]
class Mug
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $tornMugId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(inversedBy: 'mugs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TornUser $defender = null;

    #[ORM\Column]
    private ?int $moneyMugged = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTornMugId(): ?string
    {
        return $this->tornMugId;
    }

    public function setTornMugId(string $tornMugId): static
    {
        $this->tornMugId = $tornMugId;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): static
    {
        $this->dateTime = $dateTime;

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

    public function getMoneyMugged(): ?int
    {
        return $this->moneyMugged;
    }

    public function setMoneyMugged(int $moneyMugged): static
    {
        $this->moneyMugged = $moneyMugged;

        return $this;
    }
}

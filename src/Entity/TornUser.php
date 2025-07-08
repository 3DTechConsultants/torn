<?php

namespace App\Entity;

use App\Repository\TornUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TornUserRepository::class)]
class TornUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $tornId = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastAction = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $signupDate = null;

    #[ORM\Column(length: 255)]
    private ?string $job = null;

    #[ORM\Column]
    private ?int $life = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?bool $starred = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastAttackDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    /**
     * @var Collection<int, TornAttack>
     */
    #[ORM\OneToMany(targetEntity: TornAttack::class, mappedBy: 'attacker', orphanRemoval: true)]
    private Collection $tornAttacks;

    /**
     * @var Collection<int, TornAttack>
     */
    #[ORM\OneToMany(targetEntity: TornAttack::class, mappedBy: 'defender', orphanRemoval: true)]
    private Collection $defender;

    /**
     * @var Collection<int, Mug>
     */
    #[ORM\OneToMany(targetEntity: Mug::class, mappedBy: 'defender', orphanRemoval: true)]
    private Collection $mugs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $property = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastUpdate = null;

    #[ORM\Column(nullable: true)]
    private ?int $netWorth = null;

    public function __construct()
    {
        $this->tornAttacks = new ArrayCollection();
        $this->defender = new ArrayCollection();
        $this->mugs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTornId(): ?int
    {
        return $this->tornId;
    }

    public function setTornId(int $tornId): static
    {
        $this->tornId = $tornId;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }


    public function getLastAction(): ?\DateTimeInterface
    {
        return $this->lastAction;
    }

    public function setLastAction(\DateTimeInterface $lastAction): static
    {
        $this->lastAction = $lastAction;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSignupDate(): ?\DateTimeInterface
    {
        return $this->signupDate;
    }

    public function setSignupDate(\DateTimeInterface $signupDate): static
    {
        $this->signupDate = $signupDate;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getLife(): ?int
    {
        return $this->life;
    }

    public function setLife(int $life): static
    {
        $this->life = $life;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPlayDuration(): ?int
    {
        if ($this->signupDate && $this->lastAction) {
            $interval = $this->signupDate->diff($this->lastAction);
            return $interval->days;
        }
        return null;
    }

    public function isStarred(): ?bool
    {
        return $this->starred;
    }

    public function setStarred(?bool $starred): static
    {
        $this->starred = $starred;

        return $this;
    }

    public function getAttackability(): ?int
    {
        $play_impact = log($this->getPlayDuration() + 1) * .2;
        $job_impact = $this->getJob() == 'None' ? 0 : 5;
        $level_impact = (100 - $this->getLevel()) * .4;
        return intval($play_impact + $job_impact + $level_impact);
    }

    public function getLastAttackDate(): ?\DateTimeInterface
    {
        return $this->lastAttackDate;
    }

    public function setLastAttackDate(?\DateTimeInterface $lastAttackDate): static
    {
        $this->lastAttackDate = $lastAttackDate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection<int, TornAttack>
     */
    public function getTornAttacks(): Collection
    {
        return $this->tornAttacks;
    }

    public function addTornAttack(TornAttack $tornAttack): static
    {
        if (!$this->tornAttacks->contains($tornAttack)) {
            $this->tornAttacks->add($tornAttack);
            $tornAttack->setAttacker($this);
        }

        return $this;
    }

    public function removeTornAttack(TornAttack $tornAttack): static
    {
        if ($this->tornAttacks->removeElement($tornAttack)) {
            // set the owning side to null (unless already changed)
            if ($tornAttack->getAttacker() === $this) {
                $tornAttack->setAttacker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TornAttack>
     */
    public function getDefender(): Collection
    {
        return $this->defender;
    }

    public function addDefender(TornAttack $defender): static
    {
        if (!$this->defender->contains($defender)) {
            $this->defender->add($defender);
            $defender->setDefender($this);
        }

        return $this;
    }

    public function removeDefender(TornAttack $defender): static
    {
        if ($this->defender->removeElement($defender)) {
            // set the owning side to null (unless already changed)
            if ($defender->getDefender() === $this) {
                $defender->setDefender(null);
            }
        }

        return $this;
    }

    public function getLastDefence(): ?\DateTimeInterface
    {
        if ($this->defender->first()) {
            return $this->defender->first()->getDateTimeStarted();
        }
        return null;
    }

    /**
     * @return Collection<int, Mug>
     */
    public function getMugs(): Collection
    {
        return $this->mugs;
    }

    public function addMug(Mug $mug): static
    {
        if (!$this->mugs->contains($mug)) {
            $this->mugs->add($mug);
            $mug->setDefender($this);
        }

        return $this;
    }

    public function removeMug(Mug $mug): static
    {
        if ($this->mugs->removeElement($mug)) {
            // set the owning side to null (unless already changed)
            if ($mug->getDefender() === $this) {
                $mug->setDefender(null);
            }
        }

        return $this;
    }
    public function getMoneyMugged(): int
    {
        $total = 0;
        foreach ($this->getMugs() as $mug) {
            $total += $mug->getMoneymugged();
        }
        return $total;
    }

    public function getNextMugDateTime(): ?\DateTimeInterface
    {
        $latest = null;
        foreach ($this->getMugs() as $mug) {
            $mugTime = $mug->getDateTime(); // Assumes Mug entity has getDateTime()
            if ($latest === null || $mugTime > $latest) {
                $latest = $mugTime;
            }
        }
        if ($latest === null) {
            return null;
        }
        $nextMugTime = clone $latest;
        $nextMugTime->modify('+24 hours');
        return $nextMugTime;
    }

    public function isMuggable(): bool
    {
        $now = new \DateTime();
        $nextMugTime = $this->getNextMugDateTime();
        if ($nextMugTime === null) {
            return false; // No mugs, so not muggable
        }

        return $nextMugTime < $now;
    }

    public function getNextMugTimeTill(): string
    {
        $nextMugTime = $this->getNextMugDateTime();
        if ($nextMugTime === null) {
            return 'Never';
        }
        if ($nextMugTime < new \DateTime()) {
            return 'Now';
        }

        $now = new \DateTime();
        $interval = $now->diff($nextMugTime);
        return $interval->format('%h hours %i minutes');
    }

    public function getAverageMoneyMugged(): float
    {
        $mugs = $this->getMugs();
        $count = count($mugs);
        if ($count === 0) {
            return 0.0;
        }
        $total = 0;
        foreach ($mugs as $mug) {
            $total += $mug->getMoneymugged();
        }
        return $total / $count;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(?string $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getNetWorth(): ?int
    {
        return $this->netWorth;
    }

    public function setNetWorth(?int $netWorth): static
    {
        $this->netWorth = $netWorth;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PointsRepository")
 */
class Points
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="points", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $moneySpent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberOfServices;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMoneySpent(): ?int
    {
        return $this->moneySpent;
    }

    public function setMoneySpent(?int $moneySpent): self
    {
        $this->moneySpent = $moneySpent;

        return $this;
    }

    public function getNumberOfServices(): ?int
    {
        return $this->numberOfServices;
    }

    public function setNumberOfServices(?int $numberOfServices): self
    {
        $this->numberOfServices = $numberOfServices;

        return $this;
    }
}

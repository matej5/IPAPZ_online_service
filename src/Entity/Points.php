<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\PointsRepository")
 */
class Points
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\OneToOne(targetEntity="App\Entity\User",
     *     inversedBy="points", cascade={"persist", "remove"})
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $moneySpent;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $numberOfServices;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): \App\Entity\User
    {
        return $this->user;
    }

    public function setUser(\App\Entity\User $user): self
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

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\WorkerRepository")
 */
class Worker
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @var                                          User
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="worker")
     */
    private $receipts;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Office", inversedBy="worker")
     */
    private $office;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Service", mappedBy="boss")
     */
    private $services;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $workTime;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $workDays;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $startTime;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $firmName;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Office", mappedBy="owner")
     */
    private $officesCreated;

    public function __construct()
    {
        $this->receipts = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->officesCreated = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->user->getFirstname() . ' ' . $this->user->getLastname();
    }

    /**
     * @return Collection|Receipt[]
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }

    public function addReceipt(\App\Entity\Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
            $receipt->setWorker($this);
        }

        return $this;
    }

    public function removeReceipt(\App\Entity\Receipt $receipt): self
    {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            // set the owning side to null (unless already changed)
            if ($receipt->getWorker() === $this) {
                $receipt->setWorker(null);
            }
        }

        return $this;
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

    public function getOffice(): \App\Entity\Office
    {
        return $this->office;
    }

    public function setOffice(\App\Entity\Office $office): self
    {
        $this->office = $office;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(\App\Entity\Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setBoss($this);
        }

        return $this;
    }

    public function removeService(\App\Entity\Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getBoss() === $this) {
                $service->setBoss(null);
            }
        }

        return $this;
    }

    public function getWorkTime(): ?int
    {
        return $this->workTime;
    }

    public function setWorkTime(int $workTime): self
    {
        $this->workTime = $workTime;

        return $this;
    }

    public function getWorkDays(): ?int
    {
        return $this->workDays;
    }

    public function setWorkDays(int $workDays): self
    {
        $this->workDays = $workDays;

        return $this;
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function setFirmName(string $firmName): self
    {
        $this->firmName = $firmName;

        return $this;
    }

    /**
     * @return Collection|Office[]
     */
    public function getOfficesCreated(): Collection
    {
        return $this->officesCreated;
    }

    public function addOfficesCreated(\App\Entity\Office $officesCreated): self
    {
        if (!$this->officesCreated->contains($officesCreated)) {
            $this->officesCreated[] = $officesCreated;
            $officesCreated->setOwner($this);
        }

        return $this;
    }

    public function removeOfficesCreated(\App\Entity\Office $officesCreated): self
    {
        if ($this->officesCreated->contains($officesCreated)) {
            $this->officesCreated->removeElement($officesCreated);
            // set the owning side to null (unless already changed)
            if ($officesCreated->getOwner() === $this) {
                $officesCreated->setOwner(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WorkerRepository")
 */
class Worker
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @var User
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="worker")
     */
    private $receipts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Office", inversedBy="worker")
     */
    private $office;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Service", mappedBy="boss")
     */
    private $services;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $workTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $workDays;

    /**
     * @ORM\Column(type="integer")
     */
    private $startTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firmName;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Office", mappedBy="owner")
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

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
            $receipt->setWorker($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
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
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
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

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setBoss($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
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

    public function addOfficesCreated(Office $officesCreated): self
    {
        if (!$this->officesCreated->contains($officesCreated)) {
            $this->officesCreated[] = $officesCreated;
            $officesCreated->setOwner($this);
        }

        return $this;
    }

    public function removeOfficesCreated(Office $officesCreated): self
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

    public function __toString() {
        return $this->getName();
    }
}

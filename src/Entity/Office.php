<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\OfficeRepository")
 */
class Office
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $state;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $city;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $address;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $phoneNumber;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="office")
     */
    private $receipts;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Worker", mappedBy="office")
     */
    private $worker;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Worker", inversedBy="officesCreated")
     */
    private $owner;

    public function __construct()
    {
        $this->receipts = new ArrayCollection();
        $this->worker = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
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
            $receipt->setOffice($this);
        }

        return $this;
    }

    public function removeReceipt(\App\Entity\Receipt $receipt): self
    {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            // set the owning side to null (unless already changed)
            if ($receipt->getOffice() === $this) {
                $receipt->setOffice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Worker[]
     */
    public function getWorker(): Collection
    {
        return $this->worker;
    }

    public function addWorker(\App\Entity\Worker $worker): self
    {
        if (!$this->worker->contains($worker)) {
            $this->worker[] = $worker;
            $worker->setOffice($this);
        }

        return $this;
    }

    public function removeWorker(\App\Entity\Worker $worker): self
    {
        if ($this->worker->contains($worker)) {
            $this->worker->removeElement($worker);
            // set the owning side to null (unless already changed)
            if ($worker->getOffice() === $this) {
                $worker->setOffice(null);
            }
        }

        return $this;
    }

    public function getOwner(): \App\Entity\Worker
    {
        return $this->owner;
    }

    public function setOwner(\App\Entity\Worker $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function __toString()
    {
        return $this->address . ', ' . $this->city;
    }
}

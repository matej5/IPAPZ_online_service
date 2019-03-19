<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfficeRepository")
 */
class Office
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="office")
     */
    private $receipts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Worker", mappedBy="office")
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Worker", inversedBy="officesCreated")
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

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
            $receipt->setOffice($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
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

    public function addWorker(Worker $worker): self
    {
        if (!$this->worker->contains($worker)) {
            $this->worker[] = $worker;
            $worker->setOffice($this);
        }

        return $this;
    }

    public function removeWorker(Worker $worker): self
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

    public function getOwner(): ?Worker
    {
        return $this->owner;
    }

    public function setOwner(?Worker $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function __toString() {
        return $this->address . ', ' . $this->city;
    }
}

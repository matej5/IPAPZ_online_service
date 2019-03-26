<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\ReceiptRepository")
 */
class Receipt
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Office", inversedBy="receipts")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $office;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="receipts")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    private $buyer;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Worker", inversedBy="receipts")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $worker;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     */
    private $startOfService;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Service", inversedBy="receipts")
     */
    private $service;

    public function __construct()
    {
        $this->service = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffice(): ?\App\Entity\Office
    {
        return $this->office;
    }

    public function setOffice(\App\Entity\Office $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getBuyer(): ?\App\Entity\User
    {
        return $this->buyer;
    }

    public function setBuyer(\App\Entity\User $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getWorker(): ?\App\Entity\Worker
    {
        return $this->worker;
    }

    public function setWorker(\App\Entity\Worker $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

    public function getStartOfService(): ?\DateTimeInterface
    {
        return $this->startOfService;
    }

    public function setStartOfService(\DateTimeInterface $startOfService): self
    {
        $this->startOfService = $startOfService;

        return $this;
    }

    public function getService(): \App\Entity\Service
    {
        return $this->service;
    }

    public function setService(\App\Entity\Service $service): self
    {
        $this->service = $service;

        return $this;
    }
}

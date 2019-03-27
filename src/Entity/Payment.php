<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $paypal;

    /**
     * @Doctrine\ORM\Mapping\Column(type="boolean")
     */
    private $pouzece;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaypal(): ?bool
    {
        return $this->paypal;
    }

    public function setPaypal(bool $paypal): self
    {
        $this->paypal = $paypal;

        return $this;
    }

    public function getPouzece(): ?bool
    {
        return $this->pouzece;
    }

    public function setPouzece(bool $pouzece): self
    {
        $this->pouzece = $pouzece;

        return $this;
    }
}

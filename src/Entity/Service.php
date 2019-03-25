<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service
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
    private $name;

    /**
     * @Doctrine\ORM\Mapping\Column(type="float")
     */
    private $cost;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\User", mappedBy="services")
     */
    private $users;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Worker", inversedBy="services")
     */
    private $boss;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $status;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $duration;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=500)
     */
    private $description;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string",       length=255, nullable=true)
     * @Symfony\Component\Validator\Constraints\NotBlank(message="Upload your image")
     * @Symfony\Component\Validator\Constraints\File(mimeTypes={         "image/png", "image/jpeg" })
     */
    private $image;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\Category", inversedBy="services")
     */
    private $category;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $catalog;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="service")
     */
    private $receipts;

    public function __construct()
    {
        $this->receipts = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(\App\Entity\User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addService($this);
        }

        return $this;
    }

    public function removeUser(\App\Entity\User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeService($this);
        }

        return $this;
    }

    public function getBoss(): \App\Entity\Worker
    {
        return $this->boss;
    }

    public function setBoss(\App\Entity\Worker $boss): self
    {
        $this->boss = $boss;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(\App\Entity\Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(\App\Entity\Category $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    public function getCatalog(): ?string
    {
        return $this->catalog;
    }

    public function setCatalog(string $catalog): self
    {
        $this->catalog = $catalog;

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
            $receipt->setService($this);
        }

        return $this;
    }

    public function removeReceipt(\App\Entity\Receipt $receipt): self
    {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            // set the owning side to null (unless already changed)
            if ($receipt->getService() === $this) {
                $receipt->setService(null);
            }
        }

        return $this;
    }
}

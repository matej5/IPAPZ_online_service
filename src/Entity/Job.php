<?php

namespace App\Entity;


/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="jobs")
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="jobsRequest")
     */
    private $worker;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $firmName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?\App\Entity\User
    {
        return $this->user;
    }

    public function setUser(?\App\Entity\User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getWorker(): ?\App\Entity\User
    {
        return $this->worker;
    }

    public function setWorker(?\App\Entity\User $worker): self
    {
        $this->worker = $worker;

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
}

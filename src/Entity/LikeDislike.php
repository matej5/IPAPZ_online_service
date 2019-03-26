<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\LikeDislikeRepository")
 */
class LikeDislike
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="likeDislikes")
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Post", inversedBy="likeDislikes")
     * @Doctrine\ORM\Mapping\JoinColumn(referencedColumnName="id",     onDelete="CASCADE")
     */
    private $post;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    private $type;

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

    public function getPost(): \App\Entity\Post
    {
        return $this->post;
    }

    public function setPost(\App\Entity\Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }
}

<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Comment", inversedBy="reports")
     */
    private $comment;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="App\Entity\Post", inversedBy="reports")
     */
    private $post;

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

    public function getComment(): \App\Entity\Comment
    {
        return $this->comment;
    }

    public function setComment(\App\Entity\Comment $comment): self
    {
        $this->comment = $comment;

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
}

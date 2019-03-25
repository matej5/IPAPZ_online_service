<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @Doctrine\ORM\Mapping\Column(type="json")
     */
    private $roles = [];

    /**
     * @var                       string The hashed password
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $password;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @Doctrine\ORM\Mapping\OneToOne(targetEntity="App\Entity\Points", mappedBy="user", cascade={"persist", "remove"})
     */
    private $points;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Report", mappedBy="user", orphanRemoval=true)
     */
    private $reports;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Post", mappedBy="user")
     */
    private $posts;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="buyer")
     */
    private $receipts;

    /**
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="App\Entity\LikeDislike", mappedBy="user")
     */
    private $likeDislikes;

    /**
     * @Doctrine\ORM\Mapping\Column(type="float")
     */
    private $money;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="App\Entity\Service", inversedBy="users")
     */
    private $services;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $image;

    /**
     * @Doctrine\ORM\Mapping\OneToOne(targetEntity="App\Entity\Worker", cascade={"persist", "remove"})
     */
    private $worker;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->receipts = new ArrayCollection();
        $this->likeDislikes = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPoints(): \App\Entity\Points
    {
        return $this->points;
    }

    public function setPoints(\App\Entity\Points $points): self
    {
        $this->points = $points;

        // set the owning side of the relation if necessary
        if ($this !== $points->getUser()) {
            $points->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(\App\Entity\Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(\App\Entity\Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(\App\Entity\Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(\App\Entity\Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getUser() === $this) {
                $report->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(\App\Entity\Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(\App\Entity\Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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
            $receipt->setBuyer($this);
        }

        return $this;
    }

    public function removeReceipt(\App\Entity\Receipt $receipt): self
    {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            // set the owning side to null (unless already changed)
            if ($receipt->getBuyer() === $this) {
                $receipt->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LikeDislike[]
     */
    public function getLikeDislikes(): Collection
    {
        return $this->likeDislikes;
    }

    public function addLikeDislike(\App\Entity\LikeDislike $likeDislike): self
    {
        if (!$this->likeDislikes->contains($likeDislike)) {
            $this->likeDislikes[] = $likeDislike;
            $likeDislike->setUser($this);
        }

        return $this;
    }

    public function removeLikeDislike(\App\Entity\LikeDislike $likeDislike): self
    {
        if ($this->likeDislikes->contains($likeDislike)) {
            $this->likeDislikes->removeElement($likeDislike);
            // set the owning side to null (unless already changed)
            if ($likeDislike->getUser() === $this) {
                $likeDislike->setUser(null);
            }
        }

        return $this;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(float $money): self
    {
        $this->money = $money;

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
        }

        return $this;
    }

    public function removeService(\App\Entity\Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function createAvatar()
    {
        $fn = $this->firstname;
        $ln = $this->lastname;
        $em = $this->email;
        $siteRoot = __DIR__ . '/../../public/images/';

        $newUserSubfolder = $siteRoot . $em;
        if (!file_exists($newUserSubfolder)) {
            mkdir($newUserSubfolder, 0777, true);
        }

        $fnInt = 0;
        $lnInt = 0;
        $emInt = 0;

        $x = strlen($fn);
        $y = strlen($ln);

        for ($i = 0; $i < $x - 1; $i++) {
            $fnInt += ord($fn[$i]);
        }

        for ($i = 0; $i < $y - 1; $i++) {
            $lnInt += ord($ln[$i]);
        }

        for ($i = 0; $em[$i] != '@'; $i++) {
            $emInt += ord($em[$i]);
        }

        $fnColor = $fnInt;
        $lnColor = $lnInt;
        $emColor = $emInt;

        while ($fnColor > 235) {
            $fnColor = $fnColor / 2 + 40;
        }

        while ($lnColor > 235) {
            $lnColor = $lnColor / 2 + 40;
        }

        while ($emColor > 235) {
            $emColor = $emColor / 2 + 40;
        }

        $total = ($fnInt + $lnInt + $emInt) * 21;
        $im = imagecreate(420, 420);
        $white = ImageColorAllocate($im, 255, 255, 255);
        $color = ImageColorAllocate($im, $fnColor, $lnColor, $emColor);
        ImageFilledRectangle($im, 0, 0, 420, 420, $white);

        $this->draw($im, $total, $color);

        $save = $newUserSubfolder . '/avatar.jpeg';
        imagejpeg($im, $save, 100);   //Saves the image

        imagedestroy($im);
    }

    public function draw($im, $total, $color)
    {
        $startX = 35;
        $startY = 35;
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if (pow(2, $i * 3 + $i) & $total) {
                    ImageFilledRectangle($im, $startX, $startY, $startX + 70, $startY + 70, $color);
                    ImageFilledRectangle($im, 385 - $startX * $i, $startY, 315 - $startX * $i, $startY + 70, $color);
                }
            }

            $startY += 70;
        }

        return $im;
    }

    public function getWorker(): \App\Entity\Worker
    {
        return $this->worker;
    }

    public function setWorker(\App\Entity\Worker $worker): self
    {
        $this->worker = $worker;

        return $this;
    }
}

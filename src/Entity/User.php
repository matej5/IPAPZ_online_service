<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Points", mappedBy="user", cascade={"persist", "remove"})
     */
    private $points;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="user", orphanRemoval=true)
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="user")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="buyer")
     */
    private $receipts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LikeDislike", mappedBy="user")
     */
    private $likeDislikes;

    /**
     * @ORM\Column(type="float")
     */
    private $money;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Service", inversedBy="users")
     */
    private $services;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;


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
        return (string) $this->email;
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
        return (string) $this->password;
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

    public function getPoints(): ?Points
    {
        return $this->points;
    }

    public function setPoints(Points $points): self
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

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
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

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
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

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
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

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
            $receipt->setBuyer($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
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

    public function addLikeDislike(LikeDislike $likeDislike): self
    {
        if (!$this->likeDislikes->contains($likeDislike)) {
            $this->likeDislikes[] = $likeDislike;
            $likeDislike->setUser($this);
        }

        return $this;
    }

    public function removeLikeDislike(LikeDislike $likeDislike): self
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

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Service $service): self
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
        $siteRoot = '/home/matej/zavrsni/public/images/';

        $newUserSubfolder = $siteRoot . $em;
        if (!file_exists($newUserSubfolder)) {
            mkdir($newUserSubfolder, 0777, true);
        }
        $fnInt = 0;
        $lnInt = 0;
        $emInt = 0;

        for ($i = 0; $i < strlen($fn) - 1; $i++) {
            $fnInt += ord($fn[$i]);
        }
        for ($i = 0; $i < strlen($ln) - 1; $i++) {
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
        for ($i = 2, $j = 0; $j < 16; $j++) {
            if (pow($i, $j) & $total) {
                switch ($j) {
                    case 0:
                        ImageFilledRectangle($im, 315, 35, 385, 105, $color);
                        ImageFilledRectangle($im, 35, 35, 105, 105, $color);
                        break;

                    case 1:
                        ImageFilledRectangle($im, 105, 35, 175, 105, $color);
                        ImageFilledRectangle($im, 245, 35, 315, 105, $color);
                        break;

                    case 2:
                        ImageFilledRectangle($im, 175, 35, 245, 105, $color);
                        break;

                    case 3:
                        ImageFilledRectangle($im, 315, 105, 385, 175, $color);
                        ImageFilledRectangle($im, 35, 105, 105, 175, $color);
                        break;

                    case 4:
                        ImageFilledRectangle($im, 245, 105, 315, 175, $color);
                        ImageFilledRectangle($im, 105, 105, 175, 175, $color);
                        break;

                    case 5:
                        ImageFilledRectangle($im, 175, 105, 245, 175, $color);
                        break;

                    case 6:
                        ImageFilledRectangle($im, 315, 175, 385, 245, $color);
                        ImageFilledRectangle($im, 35, 175, 105, 245, $color);
                        break;

                    case 7:
                        ImageFilledRectangle($im, 245, 175, 315, 245, $color);
                        ImageFilledRectangle($im, 105, 175, 175, 245, $color);
                        break;

                    case 8:
                        ImageFilledRectangle($im, 175, 175, 245, 245, $color);
                        break;

                    case 9:
                        ImageFilledRectangle($im, 315, 245, 385, 315, $color);
                        ImageFilledRectangle($im, 35, 245, 105, 315, $color);
                        break;

                    case 10:
                        ImageFilledRectangle($im, 245, 245, 315, 315, $color);
                        ImageFilledRectangle($im, 105, 245, 175, 315, $color);
                        break;

                    case 11:
                        ImageFilledRectangle($im, 175, 245, 245, 315, $color);
                        break;

                    case 12:
                        ImageFilledRectangle($im, 315, 315, 385, 385, $color);
                        ImageFilledRectangle($im, 35, 315, 105, 385, $color);
                        break;

                    case 13:
                        ImageFilledRectangle($im, 245, 315, 315, 385, $color);
                        ImageFilledRectangle($im, 105, 315, 175, 385, $color);
                        break;

                    case 14:
                        ImageFilledRectangle($im, 175, 315, 245, 385, $color);
                        break;

                }
            }
        }

        $save = $newUserSubfolder . '/avatar.jpeg';
        imagejpeg($im, $save, 100);   //Saves the image

        imagedestroy($im);
    }
}

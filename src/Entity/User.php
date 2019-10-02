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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentProfil", mappedBy="user")
     */
    private $payment_profil;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Img", mappedBy="user")
     */
    private $img;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profil", cascade={"persist", "remove"})
     */
    private $profil;

    /**
     * @ORM\Column(type="integer")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Subscribe", cascade={"persist", "remove"})
     */
    private $subscribe;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Items", inversedBy="users")
     */
    private $items;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="user")
     */
    private $payments;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $flowerNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $favoriteNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Slug", mappedBy="author", orphanRemoval=true)
     */
    private $slugs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pseudo;

    public function __construct()
    {
        $this->payment_profil = new ArrayCollection();
        $this->img = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->slugs = new ArrayCollection();
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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    /**
     * @return Collection|PaymentProfil[]
     */
    public function getPaymentProfil(): Collection
    {
        return $this->payment_profil;
    }

    public function addPaymentProfil(PaymentProfil $paymentProfil): self
    {
        if (!$this->payment_profil->contains($paymentProfil)) {
            $this->payment_profil[] = $paymentProfil;
            $paymentProfil->setUser($this);
        }

        return $this;
    }

    public function removePaymentProfil(PaymentProfil $paymentProfil): self
    {
        if ($this->payment_profil->contains($paymentProfil)) {
            $this->payment_profil->removeElement($paymentProfil);
            // set the owning side to null (unless already changed)
            if ($paymentProfil->getUser() === $this) {
                $paymentProfil->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Img[]
     */
    public function getImg(): Collection
    {
        return $this->img;
    }

    public function addImg(Img $img): self
    {
        if (!$this->img->contains($img)) {
            $this->img[] = $img;
            $img->setUser($this);
        }

        return $this;
    }

    public function removeImg(Img $img): self
    {
        if ($this->img->contains($img)) {
            $this->img->removeElement($img);
            // set the owning side to null (unless already changed)
            if ($img->getUser() === $this) {
                $img->setUser(null);
            }
        }

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getSubscribe(): ?Subscribe
    {
        return $this->subscribe;
    }

    public function setSubscribe(?Subscribe $subscribe): self
    {
        $this->subscribe = $subscribe;

        return $this;
    }

    /**
     * @return Collection|Items[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Items $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    public function removeItem(Items $item): self
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    public function getFlowerNumber(): ?int
    {
        return $this->flowerNumber;
    }

    public function setFlowerNumber(?int $flowerNumber): self
    {
        $this->flowerNumber = $flowerNumber;

        return $this;
    }

    public function getFavoriteNumber(): ?int
    {
        return $this->favoriteNumber;
    }

    public function setFavoriteNumber(?int $favoriteNumber): self
    {
        $this->favoriteNumber = $favoriteNumber;

        return $this;
    }

    public function removeMessage(Messages $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            $message->removeMessageTo($this);
        }

        return $this;
    }

    /**
     * @return Collection|Slug[]
     */
    public function getSlugs(): Collection
    {
        return $this->slugs;
    }

    public function addSlug(Slug $slug): self
    {
        if (!$this->slugs->contains($slug)) {
            $this->slugs[] = $slug;
            $slug->setAuthor($this);
        }

        return $this;
    }

    public function removeSlug(Slug $slug): self
    {
        if ($this->slugs->contains($slug)) {
            $this->slugs->removeElement($slug);
            // set the owning side to null (unless already changed)
            if ($slug->getAuthor() === $this) {
                $slug->setAuthor(null);
            }
        }

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemsRepository")
 */
class Items
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
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Subscribe", mappedBy="item", cascade={"persist", "remove"})
     */
    private $subscribe;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isASubscribe;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="items")
     */
    private $users;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSubscribe(): ?Subscribe
    {
        return $this->subscribe;
    }

    public function setSubscribe(?Subscribe $subscribe): self
    {
        $this->subscribe = $subscribe;

        // set (or unset) the owning side of the relation if necessary
        $newItem = $subscribe === null ? null : $this;
        if ($newItem !== $subscribe->getItem()) {
            $subscribe->setItem($newItem);
        }

        return $this;
    }

    public function getIsASubscribe(): ?bool
    {
        return $this->isASubscribe;
    }

    public function setIsASubscribe(bool $isASubscribe): self
    {
        $this->isASubscribe = $isASubscribe;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addItem($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeItem($this);
        }

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }
}

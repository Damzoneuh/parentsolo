<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscribeRepository")
 */
class Subscribe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deadline;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Items", inversedBy="subscribe", cascade={"persist", "remove"})
     */
    private $item;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plan;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getItem(): ?Items
    {
        return $this->item;
    }

    public function setItem(?Items $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(?string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }
}

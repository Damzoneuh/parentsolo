<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupsRepository")
 */
class Groups
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="json_array")
     */
    private $members = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Slug", mappedBy="groups")
     */
    private $slugs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValidated;

    public function __construct()
    {
        $this->slugs = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMembers(): ?array
    {
        return $this->members;
    }

    public function setMembers(array $members): self
    {
        $this->members = $members;

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
            $slug->addGroup($this);
        }

        return $this;
    }

    public function removeSlug(Slug $slug): self
    {
        if ($this->slugs->contains($slug)) {
            $this->slugs->removeElement($slug);
            $slug->removeGroup($this);
        }

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;

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
}

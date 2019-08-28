<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SizeRepository")
 */
class Size
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profil", mappedBy="size", cascade={"persist", "remove"})
     */
    private $profil;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        // set (or unset) the owning side of the relation if necessary
        $newSize = $profil === null ? null : $this;
        if ($newSize !== $profil->getSize()) {
            $profil->setSize($newSize);
        }

        return $this;
    }
}

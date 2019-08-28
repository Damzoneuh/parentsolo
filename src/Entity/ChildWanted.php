<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildWantedRepository")
 */
class ChildWanted
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
    private $number;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profil", mappedBy="child_wanted", cascade={"persist", "remove"})
     */
    private $profil;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

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
        $newChild_wanted = $profil === null ? null : $this;
        if ($newChild_wanted !== $profil->getChildWanted()) {
            $profil->setChildWanted($newChild_wanted);
        }

        return $this;
    }
}

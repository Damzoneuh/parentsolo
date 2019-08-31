<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfilRepository")
 */
class Profil
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_man;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Childs", mappedBy="profil")
     */
    private $childs;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Nationality", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $nationality;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Studies", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $studies;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Status", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\LifeStyle", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $lifestyle;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ChildGard", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $child_gard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Smoke", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $smoke;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Temperament", cascade={"persist", "remove"}, inversedBy="profils")
     */
    private $temperament;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Religion", cascade={"persist", "remove"}, mappedBy="profil")
     */
    private $religion;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Activity", cascade={"persist", "remove"}, mappedBy="profil")
     */
    private $activity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Langages", mappedBy="profil")
     */
    private $langages;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Relationship", cascade={"persist", "remove"})
     */
    private $relationship;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ChildWanted", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $child_wanted;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Wedding", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $wedding;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Size", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $size;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Eyes", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $eyes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Origin", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $origin;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Weight", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $weight;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Hair", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $hair;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Silhouette", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $silhouette;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\HairStyle", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $hair_style;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Cook", inversedBy="profils")
     */
    private $cook;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Outing", inversedBy="profils")
     */
    private $outing;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Hobbies", inversedBy="profils")
     */
    private $hobbies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sport", inversedBy="profils")
     */
    private $sports;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Music", inversedBy="profils")
     */
    private $music;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movies", inversedBy="profils")
     */
    private $movies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Read", inversedBy="profils")
     */
    private $reading;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Pets", inversedBy="profils")
     */
    private $pets;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Favorite", inversedBy="profils")
     */
    private $favorite;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Canton", inversedBy="profil", cascade={"persist", "remove"})
     */
    private $canton;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
        $this->langages = new ArrayCollection();
        $this->cook = new ArrayCollection();
        $this->outing = new ArrayCollection();
        $this->hobbies = new ArrayCollection();
        $this->sports = new ArrayCollection();
        $this->music = new ArrayCollection();
        $this->movies = new ArrayCollection();
        $this->reading = new ArrayCollection();
        $this->pets = new ArrayCollection();
        $this->favorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsMan(): ?bool
    {
        return $this->is_man;
    }

    public function setIsMan(bool $is_man): self
    {
        $this->is_man = $is_man;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Childs[]
     */
    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(Childs $child): self
    {
        if (!$this->childs->contains($child)) {
            $this->childs[] = $child;
            $child->setProfil($this);
        }

        return $this;
    }

    public function removeChild(Childs $child): self
    {
        if ($this->childs->contains($child)) {
            $this->childs->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getProfil() === $this) {
                $child->setProfil(null);
            }
        }

        return $this;
    }

    public function getNationality(): ?Nationality
    {
        return $this->nationality;
    }

    public function setNationality(?Nationality $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getStudies(): ?Studies
    {
        return $this->studies;
    }

    public function setStudies(?Studies $studies): self
    {
        $this->studies = $studies;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLifestyle(): ?LifeStyle
    {
        return $this->lifestyle;
    }

    public function setLifestyle(?LifeStyle $lifestyle): self
    {
        $this->lifestyle = $lifestyle;

        return $this;
    }

    public function getChildGard(): ?ChildGard
    {
        return $this->child_gard;
    }

    public function setChildGard(?ChildGard $child_gard): self
    {
        $this->child_gard = $child_gard;

        return $this;
    }

    public function getSmoke(): ?Smoke
    {
        return $this->smoke;
    }

    public function setSmoke(?Smoke $smoke): self
    {
        $this->smoke = $smoke;

        return $this;
    }

    public function getTemperament(): ?Temperament
    {
        return $this->temperament;
    }

    public function setTemperament(?Temperament $temperament): self
    {
        $this->temperament = $temperament;

        return $this;
    }

    public function getReligion(): ?Religion
    {
        return $this->religion;
    }

    public function setReligion(?Religion $religion): self
    {
        $this->religion = $religion;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return Collection|Langages[]
     */
    public function getLangages(): Collection
    {
        return $this->langages;
    }

    public function addLangage(Langages $langage): self
    {
        if (!$this->langages->contains($langage)) {
            $this->langages[] = $langage;
            $langage->setProfil($this);
        }

        return $this;
    }

    public function removeLangage(Langages $langage): self
    {
        if ($this->langages->contains($langage)) {
            $this->langages->removeElement($langage);
            // set the owning side to null (unless already changed)
            if ($langage->getProfil() === $this) {
                $langage->setProfil(null);
            }
        }

        return $this;
    }

    public function getRelationship(): ?Relationship
    {
        return $this->relationship;
    }

    public function setRelationship(?Relationship $relationship): self
    {
        $this->relationship = $relationship;

        return $this;
    }

    public function getChildWanted(): ?ChildWanted
    {
        return $this->child_wanted;
    }

    public function setChildWanted(?ChildWanted $child_wanted): self
    {
        $this->child_wanted = $child_wanted;

        return $this;
    }

    public function getWedding(): ?Wedding
    {
        return $this->wedding;
    }

    public function setWedding(?Wedding $wedding): self
    {
        $this->wedding = $wedding;

        return $this;
    }

    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getEyes(): ?Eyes
    {
        return $this->eyes;
    }

    public function setEyes(?Eyes $eyes): self
    {
        $this->eyes = $eyes;

        return $this;
    }

    public function getorigin(): ?Origin
    {
        return $this->origin;
    }

    public function setorigin(?Origin $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getWeight(): ?Weight
    {
        return $this->weight;
    }

    public function setWeight(?Weight $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHair(): ?Hair
    {
        return $this->hair;
    }

    public function setHair(?Hair $hair): self
    {
        $this->hair = $hair;

        return $this;
    }

    public function getSilhouette(): ?Silhouette
    {
        return $this->silhouette;
    }

    public function setSilhouette(?Silhouette $silhouette): self
    {
        $this->silhouette = $silhouette;

        return $this;
    }

    public function getHairStyle(): ?HairStyle
    {
        return $this->hair_style;
    }

    public function setHairStyle(?HairStyle $hair_style): self
    {
        $this->hair_style = $hair_style;

        return $this;
    }

    /**
     * @return Collection|Cook[]
     */
    public function getCook(): Collection
    {
        return $this->cook;
    }

    public function addCook(Cook $cook): self
    {
        if (!$this->cook->contains($cook)) {
            $this->cook[] = $cook;
        }

        return $this;
    }

    public function removeCook(Cook $cook): self
    {
        if ($this->cook->contains($cook)) {
            $this->cook->removeElement($cook);
        }

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOuting(): Collection
    {
        return $this->outing;
    }

    public function addOuting(Outing $outing): self
    {
        if (!$this->outing->contains($outing)) {
            $this->outing[] = $outing;
        }

        return $this;
    }

    public function removeOuting(Outing $outing): self
    {
        if ($this->outing->contains($outing)) {
            $this->outing->removeElement($outing);
        }

        return $this;
    }

    /**
     * @return Collection|Hobbies[]
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(Hobbies $hobby): self
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies[] = $hobby;
        }

        return $this;
    }

    public function removeHobby(Hobbies $hobby): self
    {
        if ($this->hobbies->contains($hobby)) {
            $this->hobbies->removeElement($hobby);
        }

        return $this;
    }

    /**
     * @return Collection|Sport[]
     */
    public function getSports(): Collection
    {
        return $this->sports;
    }

    public function addSport(Sport $sport): self
    {
        if (!$this->sports->contains($sport)) {
            $this->sports[] = $sport;
        }

        return $this;
    }

    public function removeSport(Sport $sport): self
    {
        if ($this->sports->contains($sport)) {
            $this->sports->removeElement($sport);
        }

        return $this;
    }

    /**
     * @return Collection|Music[]
     */
    public function getMusic(): Collection
    {
        return $this->music;
    }

    public function addMusic(Music $music): self
    {
        if (!$this->music->contains($music)) {
            $this->music[] = $music;
        }

        return $this;
    }

    public function removeMusic(Music $music): self
    {
        if ($this->music->contains($music)) {
            $this->music->removeElement($music);
        }

        return $this;
    }

    /**
     * @return Collection|Movies[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movies $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
        }

        return $this;
    }

    public function removeMovie(Movies $movie): self
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
        }

        return $this;
    }

    /**
     * @return Collection|Read[]
     */
    public function getReading(): Collection
    {
        return $this->reading;
    }

    public function addReading(Read $reading): self
    {
        if (!$this->reading->contains($reading)) {
            $this->reading[] = $reading;
        }

        return $this;
    }

    public function removeReading(Read $reading): self
    {
        if ($this->reading->contains($reading)) {
            $this->reading->removeElement($reading);
        }

        return $this;
    }

    /**
     * @return Collection|Pets[]
     */
    public function getPets(): Collection
    {
        return $this->pets;
    }

    public function addPet(Pets $pet): self
    {
        if (!$this->pets->contains($pet)) {
            $this->pets[] = $pet;
        }

        return $this;
    }

    public function removePet(Pets $pet): self
    {
        if ($this->pets->contains($pet)) {
            $this->pets->removeElement($pet);
        }

        return $this;
    }

    /**
     * @return Collection|Favorite[]
     */
    public function getFavorite(): Collection
    {
        return $this->favorite;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorite->contains($favorite)) {
            $this->favorite[] = $favorite;
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorite->contains($favorite)) {
            $this->favorite->removeElement($favorite);
        }

        return $this;
    }

    public function getCanton(): ?Canton
    {
        return $this->canton;
    }

    public function setCanton(?Canton $canton): self
    {
        $this->canton = $canton;

        return $this;
    }
}

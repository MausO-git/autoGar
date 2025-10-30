<?php

namespace App\Entity;

use App\Repository\AdRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdRepository::class)]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column(length: 255)]
    private ?string $cover = null;

    #[ORM\Column]
    private ?int $km = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $nbOwner = null;

    #[ORM\Column]
    private ?int $cylindree = null;

    #[ORM\Column]
    private ?int $power = null;

    #[ORM\Column(length: 50)]
    private ?string $carbu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $year = null;

    #[ORM\Column(length: 50)]
    private ?string $transmission = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descri = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $opt = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'ad')]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if (empty($this->slug)) {
            if (empty($this->marque) || empty($this->modele)) {
                throw new \RuntimeException('Les champs "marque" et "modele" ne peuvent pas être vides.');
            }
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->marque . '-' . $this->modele);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getKm(): ?int
    {
        return $this->km;
    }

    public function setKm(int $km): static
    {
        $this->km = $km;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getNbOwner(): ?int
    {
        return $this->nbOwner;
    }

    public function setNbOwner(int $nbOwner): static
    {
        $this->nbOwner = $nbOwner;

        return $this;
    }

    public function getCylindree(): ?int
    {
        return $this->cylindree;
    }

    public function setCylindree(int $cylindree): static
    {
        $this->cylindree = $cylindree;

        return $this;
    }

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getCarbu(): ?string
    {
        return $this->carbu;
    }

    public function setCarbu(string $carbu): static
    {
        $this->carbu = $carbu;

        return $this;
    }

    public function getYear(): ?\DateTime
    {
        return $this->year;
    }

    public function setYear(\DateTime $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(string $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getDescri(): ?string
    {
        return $this->descri;
    }

    public function setDescri(string $descri): static
    {
        $this->descri = $descri;

        return $this;
    }

    public function getOpt(): ?string
    {
        return $this->opt;
    }

    public function setOpt(string $opt): static
    {
        $this->opt = $opt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }
}

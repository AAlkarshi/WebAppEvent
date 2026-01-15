<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cp = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    /**
     * @var Collection<int, Event>
     */
    /* RELATION Une adresse peut être liée à plusieurs EVENT */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'address')]
    /* $events contient tous les EVENT liés à cette adresse. C'est un objet Collection fourni par Doctrine. */
    private Collection $events;

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getCp(): ?int {
        return $this->cp;
    }

    public function setCp(int $cp): static {
        $this->cp = $cp;

        return $this;
    }

    public function getAddress(): ?string {
        return $this->address;
    }

    public function setAddress(string $address): static {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(string $city): static {
        $this->city = $city;

        return $this;
    }

    /**
     * En Doctrine/Symfony, une Collection est une structure de données utilisée pour gérer les entités liées dans des relations OneToMany ou ManyToMany 
     * Comme un tableau d’objets, mais avec des méthodes supplémentaires pour gérer les relations entre entités

     * @return Collection<int, Event>
     */
    /* Récupère tous les événements liés à cette adresse */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /* Ajoute un EVENT à l’adresse et MAJ la relation inverse dans Event */
    public function addEvent(Event $event): static {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setAddress($this);
        }
        return $this;
    }

    /* Supprime un EVENT de l’adresse et MAJ la relation inverse */
    public function removeEvent(Event $event): static{
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getAddress() === $this) {
                $event->setAddress(null);
            }
        }
        return $this;
    }
}

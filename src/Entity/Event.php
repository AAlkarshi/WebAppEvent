<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;


#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title_event = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null; 

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_event = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description_event = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateTime_event = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration_event = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbx_participant = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbx_participant_max = null;


    /**
     * @var Collection<int, Register>
     */
    /* Plusieurs inscriptions (Register) peuvent concerner le MM EVENT */
    #[ORM\OneToMany(targetEntity: Register::class, mappedBy: 'Event')]
    private Collection $registers;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    public function __construct()
    {
        $this->registers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleEvent(): ?string
    {
        return $this->title_event;
    }

    public function setTitleEvent(string $title_event): static
    {
        $this->title_event = $title_event;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getImageEvent(): ?string
    {
        return $this->image_event;
    }

    public function setImageEvent(?string $image_event): static
    {
        $this->image_event = $image_event;

        return $this;
    }

    public function getDescriptionEvent(): ?string
    {
        return $this->description_event;
    }

    public function setDescriptionEvent(?string $description_event): static
    {
        $this->description_event = $description_event;

        return $this;
    }

    public function getDateTimeEvent(): ?\DateTimeImmutable
    {
        return $this->dateTime_event;
    }

    public function setDateTimeEvent(\DateTimeImmutable $dateTime_event): static
    {
        $this->dateTime_event = $dateTime_event;

        return $this;
    }

    public function getDurationEvent(): ?int
    {
        return $this->duration_event;
    }

    public function setDurationEvent(?int $duration_event): static
    {
        $this->duration_event = $duration_event;

        return $this;
    }

    public function getNbxParticipant(): ?int
    {
        return $this->nbx_participant;
    }

    public function setNbxParticipant(int $nbx_participant): static
    {
        $this->nbx_participant = $nbx_participant;

        return $this;
    }

    public function getNbxParticipantMax(): ?int
    {
        return $this->nbx_participant_max;
    }

    public function setNbxParticipantMax(int $nbx_participant_max): static
    {
        $this->nbx_participant_max = $nbx_participant_max;

        return $this;
    }

    /**
     * @return Collection<int, Register>
     */
    public function getRegisters(): Collection
    {
        return $this->registers;
    }

    public function addRegister(Register $register): static
    {
        if (!$this->registers->contains($register)) {
            $this->registers->add($register);
            $register->setEvent($this);
        }

        return $this;
    }

    public function removeRegister(Register $register): static
    {
        if ($this->registers->removeElement($register)) {
            // set the owning side to null (unless already changed)
            if ($register->getEvent() === $this) {
                $register->setEvent(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }
}

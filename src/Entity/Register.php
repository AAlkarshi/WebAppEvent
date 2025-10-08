<?php

namespace App\Entity;

use App\Repository\RegisterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegisterRepository::class)]
class Register
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /* l’inscription est active(true)-> participe à l'évent OU inactive(false) désinscris ou évent annulé. */
    #[ORM\Column]
    private ?bool $active = null;

    /* Plusieurs inscriptions (Register) concerne le MM User */
    #[ORM\ManyToOne(inversedBy: 'registers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /* Plusieurs inscriptions (Register) concerne le MM EVENT */
    #[ORM\ManyToOne(inversedBy: 'registers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $Event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->Event;
    }

    public function setEvent(?Event $Event): static
    {
        $this->Event = $Event;

        return $this;
    }
}

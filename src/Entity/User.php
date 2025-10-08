<?php

namespace App\Entity;

use App\Enum\GenderUser;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User 
{
    public function __construct() {
        $this->role = UserRole::User;
        $this->date_creation = new \DateTimeImmutable('today');
        $this->registers = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: GenderUser::class)]
    private ?GenderUser $gender_user = null;

    #[ORM\Column(enumType: UserRole::class)]
    private ?UserRole $role = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar_user = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname_user = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname_user = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $datebirth_user = null;

    #[ORM\Column(length: 255)]
    private ?string $mail_user = null;

    #[ORM\Column(length: 255)]
    private ?string $password_user = null;

    #[ORM\Column(length: 50)]
    private ?string $city_user = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_creation = null;

    /**
     * @var Collection<int, Register>
     */
    /* Plusieurs inscriptions (Register) peuvent concerner le même utilisateur. */
    #[ORM\OneToMany(targetEntity: Register::class, mappedBy: 'user')]
    private Collection $registers;

    /**
     * @var Collection<int, Category>
     */
    /* Un USER peut créer plusieurs catégories. */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'created')]
    private Collection $categories;


    



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenderUser(): ?GenderUser
    {
        return $this->gender_user;
    }

    public function setGenderUser(GenderUser $gender_user): static
    {
        $this->gender_user = $gender_user;

        return $this;
    }

    public function getRole(): ?UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAvatarUser(): ?string
    {
        return $this->avatar_user;
    }

    public function setAvatarUser(?string $avatar_user): static
    {
        $this->avatar_user = $avatar_user;

        return $this;
    }

    public function getLastnameUser(): ?string
    {
        return $this->lastname_user;
    }

    public function setLastnameUser(string $lastname_user): static
    {
        $this->lastname_user = $lastname_user;

        return $this;
    }

    public function getFirstnameUser(): ?string
    {
        return $this->firstname_user;
    }

    public function setFirstnameUser(string $firstname_user): static
    {
        $this->firstname_user = $firstname_user;

        return $this;
    }

    public function getDatebirthUser(): ?\DateTimeImmutable
    {
        return $this->datebirth_user;
    }

    public function setDatebirthUser(\DateTimeImmutable $datebirth_user): static
    {
        $this->datebirth_user = $datebirth_user;

        return $this;
    }

    public function getMailUser(): ?string
    {
        return $this->mail_user;
    }

    public function setMailUser(string $mail_user): static
    {
        $this->mail_user = $mail_user;

        return $this;
    }

    public function getPasswordUser(): ?string
    {
        return $this->password_user;
    }

    public function setPasswordUser(string $password_user): static
    {
        $this->password_user = $password_user;

        return $this;
    }

    public function getCityUser(): ?string
    {
        return $this->city_user;
    }

    public function setCityUser(string $city_user): static
    {
        $this->city_user = $city_user;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeImmutable
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeImmutable $date_creation): static
    {
        $this->date_creation = $date_creation;

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
            $register->setUser($this);
        }

        return $this;
    }

    public function removeRegister(Register $register): static
    {
        if ($this->registers->removeElement($register)) {
            // set the owning side to null (unless already changed)
            if ($register->getUser() === $this) {
                $register->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setCreated($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCreated() === $this) {
                $category->setCreated(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Staff\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Staff\User\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "tabel_user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Name should not be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Name cannot be longer than {{ limit }} characters."
    )]
    private $name;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    #[Assert\NotBlank(message: "Tab number should not be blank.")]
    #[Assert\Type(
        type: "integer",
        message: "Tab number must be an integer."
    )]
    private $tab_num = 0;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    #[Assert\NotBlank(message: "Group code should not be blank.")]
    #[Assert\Type(
        type: "integer",
        message: "Group code must be an integer."
    )]
    private $gr_kod = 0;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Password should not be blank.")]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Password must be at least {{ limit }} characters long.",
        maxMessage: "Password cannot be longer than {{ limit }} characters."
    )]
    private $password;

    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_HEADER = 'ROLE_HEADER';

    #[ORM\Column(type: "json")]
    #[Assert\NotNull(message: "Roles should not be null.")]
    #[Assert\All([
        new Assert\Type(
            type: "string",
            message: "Each role must be a string."
        )
    ])]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Assert\NotBlank(message: "Email should not be blank.")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private $email;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Full name should not be blank.")]
    private $fullName;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $position;

    #[ORM\Column(type: "integer", nullable: true)]
    private $grade;

    #[ORM\ManyToOne(targetEntity: ProductionType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $productionType;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $group;

    // Getters and Setters
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

    public function getTabNum(): ?int
    {
        return $this->tab_num;
    }

    public function setTabNum(int $tab_num): self
    {
        $this->tab_num = $tab_num;

        return $this;
    }

    public function getGrKod(): ?int
    {
        return $this->gr_kod;
    }

    public function setGrKod(int $gr_kod): self
    {
        $this->gr_kod = $gr_kod;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getGrade(): ?int
    {
        return $this->grade;
    }

    public function setGrade(?int $grade): self
    {
        $this->grade = $grade;
        return $this;
    }

    public function getProductionType(): ?ProductionType
    {
        return $this->productionType;
    }

    public function setProductionType(?ProductionType $productionType): self
    {
        $this->productionType = $productionType;
        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function getSalt(): ?string
    {
        // not needed for bcrypt or argon2i
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->name;
    }

    public function getUserIdentifier(): string
    {
        return $this->name;
    }
}

<?php

namespace App\User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $tab_num;

    /**
     * @ORM\Column(type="integer")
     */
    private $gr_kod;

    // Getters and Setters
    public function getId(): ?int
    {
        return $id;
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
}

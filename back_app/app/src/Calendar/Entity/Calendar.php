<?php

namespace App\Calendar\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Calendar\Repository\CalendarRepository")]
#[ORM\Table(name: "calendar")]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "date")]
    private $date;

    #[ORM\Column(type: "string", length: 1)]
    private $type; // 'W' - work, 'P' - pre-holiday, 'H' - holiday, 'R' - rest

    #[ORM\Column(type: "integer")]
    private $hours;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;
        return $this;
    }
}
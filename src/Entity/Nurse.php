<?php

namespace App\Entity;

use App\Repository\NurseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NurseRepository::class)]
class Nurse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $username = null;

    #[ORM\Column(length: 20)]
    private ?string $password = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(length:30, nullable: true)]
    private ?string $speciality = null;

    #[ORM\Column(length:30, nullable: true)]
    private ?string $shift = null;

    #[ORM\Column(length:20, nullable: true)]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(?string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getSpeciality(): ?string {
        return $this->speciality;
    }

    public function setSpeciality(?string $speciality): static {
        
        $this->speciality = $speciality;

        return $this;

    }

    public function getShift(): ?string {
        return $this->shift;
    }

    public function setShift(?string $shift): static {
        
        $this->shift = $shift;

        return $this;

    }

    public function getPhone(): ?string {
        return $this->phone;
    }

    public function setPhone(?string $phone): static {
        
        $this->phone = $phone;

        return $this;

    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): static {
        
        $this->email = $email;

        return $this;
    }
}

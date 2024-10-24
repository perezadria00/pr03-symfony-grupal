<?php

namespace App\Entity;

use App\Repository\CredentialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CredentialRepository::class)
 */
class Credential
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Nurse", inversedBy="credential")
     * @ORM\JoinColumn(name="nurse_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $nurse;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    // Getters y Setters

    public function getNurse(): ?Nurse
    {
        return $this->nurse;
    }

    public function setNurse(?Nurse $nurse): self
    {
        $this->nurse = $nurse;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
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
}

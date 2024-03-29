<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $email ;

    #[ORM\Column(length: 255)]
    private string $password ;

    #[ORM\ManyToOne(targetEntity: Rol::class, inversedBy: 'products')]
    private Rol $rol;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function getRol(): Rol
    {
        return $this->rol;
    }

    public function setRol(Rol $rol): self
    {
        $this->rol = $rol;

        return $this;
    }


}
